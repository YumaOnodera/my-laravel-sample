source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}
export PROJECT_REFERENCE_ROLE="get_projects"

# プロジェクトの参照に必要なAPIを有効化
gcloud services enable cloudresourcemanager.googleapis.com --project ${PROJECT_ID}

# プロジェクトにアクセスするためのカスタムロールを作成
gcloud iam roles create ${PROJECT_REFERENCE_ROLE} \
    --description="Project Reference" \
    --permissions=resourcemanager.projects.get \
    --project="${PROJECT_ID}" \
    --title="Project Reference"

export SERVICE_ACCOUNT="${WIF_SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"

# プロジェクト参照権限をサービスアカウントに付与する
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member=serviceAccount:${SERVICE_ACCOUNT} \
    --role=projects/${PROJECT_ID}/roles/${PROJECT_REFERENCE_ROLE}

# プロジェクト参照権限をサービスアカウントに付与する
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member=serviceAccount:${SERVICE_ACCOUNT} \
    --role=roles/serviceusage.serviceUsageConsumer
