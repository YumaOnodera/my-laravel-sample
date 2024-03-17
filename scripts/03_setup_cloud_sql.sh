source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# CloudSQLに必要なAPIを有効にする
gcloud services enable compute.googleapis.com sqladmin.googleapis.com run.googleapis.com \
	containerregistry.googleapis.com cloudbuild.googleapis.com servicenetworking.googleapis.com

# CloudSQLインスタンスを作成する
gcloud compute addresses create ${PROJECT_ID} \
    --global \
    --purpose=VPC_PEERING \
    --prefix-length=16 \
    --network=${PROJECT_ID}
gcloud services vpc-peerings connect \
    --service=servicenetworking.googleapis.com \
    --ranges=${PROJECT_ID} \
    --network=${PROJECT_ID} \
    --project=${PROJECT_ID}
gcloud sql instances create ${PROJECT_ID} \
    --database-version=MYSQL_8_0 \
    --cpu=1 \
    --memory=3.75GB \
    --region=${GCP_SQL_REGION} \
    --root-password=${GCP_SQL_INSTANCE_PASSWORD} \
    --no-assign-ip \
    --network=${PROJECT_ID}
gcloud sql instances patch ${PROJECT_ID} \
    --require-ssl

# CloudSQLデータベースを作成する
gcloud sql databases create ${DB_DATABASE} --instance=${PROJECT_ID}

# CloudSQLのデータベースユーザーを作成する
gcloud sql users create ${DB_USERNAME} \
    --instance=${DB_DATABASE}-${PROJECT_ENV} \
    --password=${DB_PASSWORD}

# CloudSQLクライアントのロールをサービスアカウントに付与する
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member="serviceAccount:${GCP_SERVICE_ACCOUNT}" \
    --role="roles/cloudsql.client"
