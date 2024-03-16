source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# プロジェクト設定
gcloud config set project ${PROJECT_ID}
