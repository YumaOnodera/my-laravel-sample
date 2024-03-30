source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}
export ROLE="get_projects"

# プロジェクトにアクセスするためのカスタムロールを作成
gcloud iam roles create ${ROLE} \
    --description="Project Reference" \
    --permissions=resourcemanager.projects.get \
    --project="${PROJECT_ID}" \
    --title="Project Reference"

# カスタムロールをサービスアカウントに紐付ける
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member=serviceAccount:"${WIF_SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role=projects/${PROJECT_ID}/roles/${ROLE}
