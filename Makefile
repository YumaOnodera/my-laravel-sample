include .env

PROJECT_ENV=stg

pre:
ifdef e
PROJECT_ENV=${e}
endif

# GCPプロジェクト作成
gcloud-create-project: pre
	gcloud projects create ${APP_NAME}-${PROJECT_ENV} ;

# GCPリポジトリ作成
gcloud-create-repository: pre
	gcloud artifacts repositories create ${GCP_REPOSITORY} \
		--repository-format=docker \
		--location=${GCP_REGION} ;

# デプロイ準備
deploy-setup: pre
	gcloud config set project ${APP_NAME}-${PROJECT_ENV} ;

# APIデプロイ
deploy-app: pre
	@make deploy-setup ;\
	gcloud builds submit --config=cloudbuild.yaml \
		--substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE=${APP_NAME},_DOCKER_FILE=Dockerfile_php;\
	gcloud run deploy ${APP_NAME} \
		--image ${GCP_REGION}-docker.pkg.dev/${APP_NAME}-${PROJECT_ENV}/${GCP_REPOSITORY}/${APP_NAME} \
		--region ${GCP_REGION} ;

# 検索エンジンデプロイ
deploy-meilisearch: pre
	@make deploy-setup ;\
	gcloud builds submit --config=cloudbuild.yaml \
		--substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE=meilisearch,_DOCKER_FILE=Dockerfile_meilisearch;\
	gcloud run deploy meilisearch \
		--port 7700 \
		--image ${GCP_REGION}-docker.pkg.dev/${APP_NAME}-${PROJECT_ENV}/${GCP_REPOSITORY}/meilisearch \
		--region ${GCP_REGION} ;
