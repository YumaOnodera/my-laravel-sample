source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

# プロジェクト設定
gcloud config set project ${APP_NAME}-${PROJECT_ENV}
