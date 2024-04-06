source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}
export PROJECT_REFERENCE_ROLE="cloud_run_deployer"

# プロジェクトの参照に必要なAPIを有効化
gcloud services enable cloudresourcemanager.googleapis.com --project ${PROJECT_ID}

# CloudRunにデプロイするためのカスタムロールを作成
gcloud iam roles create ${PROJECT_REFERENCE_ROLE} \
    --description="CloudRun Deployer" \
    --permissions=iam.serviceAccounts.actAs,resourcemanager.projects.get,run.services.get,run.services.update,serviceusage.services.use,storage.buckets.get,storage.buckets.list,storage.objects.create \
    --project="${PROJECT_ID}" \
    --title="CloudRun Deployer"

export SERVICE_ACCOUNT="${WIF_SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"

# カスタムロールをサービスアカウントに割り当てる
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member=serviceAccount:${SERVICE_ACCOUNT} \
    --role=projects/${PROJECT_ID}/roles/${PROJECT_REFERENCE_ROLE}
