source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# GCPプロジェクト作成
gcloud projects create ${PROJECT_ID} ;

# GCPリポジトリ作成
gcloud artifacts repositories create ${GCP_REPOSITORY} \
    --repository-format=docker \
    --location=${GCP_REGION}
