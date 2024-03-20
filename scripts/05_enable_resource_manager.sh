source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# プロジェクトの参照に必要なAPIを有効化
gcloud services enable cloudresourcemanager.googleapis.com --project ${PROJECT_ID}
