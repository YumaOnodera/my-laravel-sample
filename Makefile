include .env
export

PROJECT_ENV=stg

pre:
ifdef e
PROJECT_ENV=${e}
endif

echo-shell:
	echo $$SHELL ;

# GCPプロジェクト作成
gcloud-create-project: pre
	gcloud projects create ${APP_NAME}-${PROJECT_ENV} ;

# GCPリポジトリ作成
gcloud-create-repository: pre
	gcloud artifacts repositories create ${GCP_REPOSITORY} \
		--repository-format=docker \
		--location=${GCP_REGION} ;

# デプロイ準備
set-project: pre
	gcloud config set project ${APP_NAME}-${PROJECT_ENV} ;

# APIデプロイ
deploy-app: pre
	@make set-project ;\
	gcloud builds submit --config=cloudbuild.yaml \
		--substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE=${APP_NAME},_DOCKER_FILE=Dockerfile_php ;\
	gcloud run deploy ${APP_NAME} \
		--image ${GCP_REGION}-docker.pkg.dev/${APP_NAME}-${PROJECT_ENV}/${GCP_REPOSITORY}/${APP_NAME} \
		--region ${GCP_REGION} \
		--vpc-connector=${APP_NAME}-${PROJECT_ENV} \
		--vpc-egress=all-traffic \
		--set-env-vars DB_NAME=${DB_DATABASE}-${PROJECT_ENV} \
		--set-env-vars DB_USER=${DB_USERNAME} \
		--set-env-vars DB_PASS=${DB_PASSWORD} \
		--set-env-vars INSTANCE_CONNECTION_NAME=${DB_DATABASE}-${PROJECT_ENV} \
		--set-env-vars DB_PORT="3306" \
		--set-env-vars INSTANCE_HOST=${DB_HOST} \
		--set-env-vars DB_ROOT_CERT="certs/server-ca.pem" \
		--set-env-vars DB_CERT="certs/client-cert.pem" \
		--set-env-vars DB_KEY="certs/client-key.pem" \
		--set-env-vars PRIVATE_IP="TRUE" ;

# 検索エンジンデプロイ
deploy-meilisearch: pre
	@make set-project ;\
	gcloud builds submit --config=cloudbuild.yaml \
		--substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE=meilisearch,_DOCKER_FILE=Dockerfile_meilisearch ;\
	gcloud run deploy meilisearch \
		--port 7700 \
		--image ${GCP_REGION}-docker.pkg.dev/${APP_NAME}-${PROJECT_ENV}/${GCP_REPOSITORY}/meilisearch \
		--region ${GCP_REGION} ;

# CloudSQLに必要なAPIを有効にする
cloud-sql-enable-api:
	gcloud services enable compute.googleapis.com sqladmin.googleapis.com run.googleapis.com \
	containerregistry.googleapis.com cloudbuild.googleapis.com servicenetworking.googleapis.com ;

# CloudSQLインスタンスを設置するVPCネットワークを作成する
vpc-networks:
	gcloud compute networks create ${APP_NAME}-${PROJECT_ENV} \
        --subnet-mode=custom ;

# VPCネットワークにサブネットを設定する
vpc-subnets:
	gcloud compute networks subnets create ${APP_NAME}-${PROJECT_ENV} \
		--network=${APP_NAME}-${PROJECT_ENV} \
		--range=${GCP_VPC_SUBNET_RANGE} \
		--region=${GCP_REGION} ;

# VPCにアクセスするコネクタを作成する
vpc-access-connectors:
	gcloud services enable vpcaccess.googleapis.com ;\
	gcloud compute networks vpc-access connectors create ${APP_NAME}-${PROJECT_ENV} \
		--region ${GCP_REGION} \
		--subnet ${APP_NAME}-${PROJECT_ENV} ;

# CloudSQLインスタンスを作成する
cloud-sql-instances:
	gcloud compute addresses create ${APP_NAME}-${PROJECT_ENV} \
		--global \
		--purpose=VPC_PEERING \
		--prefix-length=16 \
		--network=${APP_NAME}-${PROJECT_ENV} ;\
	gcloud services vpc-peerings connect \
		--service=servicenetworking.googleapis.com \
		--ranges=${APP_NAME}-${PROJECT_ENV} \
		--network=${APP_NAME}-${PROJECT_ENV} \
		--project=${APP_NAME}-${PROJECT_ENV} ;\
 	gcloud sql instances create ${APP_NAME}-${PROJECT_ENV} \
		--database-version=MYSQL_8_0 \
		--cpu=1 \
		--memory=3.75GB \
		--region=${GCP_SQL_REGION} \
		--root-password=${DB_PASSWORD} \
		--no-assign-ip \
        --network=${APP_NAME}-${PROJECT_ENV} ;\
	gcloud sql instances patch ${APP_NAME}-${PROJECT_ENV} \
		--require-ssl ;

# CloudSQLデータベースを作成する
cloud-sql-databases:
	gcloud sql databases create ${DB_DATABASE} --instance=${APP_NAME}-${PROJECT_ENV} ;

# CloudSQLのデータベースユーザーを作成する
cloud-sql-database-users:
	gcloud sql users create ${DB_USERNAME} \
		--instance=${DB_DATABASE}-${PROJECT_ENV} \
		--password=${DB_PASSWORD} ;

# CloudSQLクライアントのロールをサービスアカウントに付与する
cloud-sql-add-iam-policy-binding:
	gcloud projects add-iam-policy-binding ${APP_NAME}-${PROJECT_ENV} \
		--member="serviceAccount:${GCP_SERVICE_ACCOUNT}" \
		--role="roles/cloudsql.client" ;

# VPC設定
vpc:
	@make set-project ;\
	@make vpc-networks ;\
	@make vpc-subnets ;\
	@make vpc-access-connectors ;

# CloudSQL設定
cloud-sql:
	@make set-project ;\
	@make cloud-sql-enable-api ;\
	@make cloud-sql-instances ;\
	@make cloud-sql-databases ;\
	@make cloud-sql-database-users ;\
	@make cloud-sql-add-iam-policy-binding ;
