source ../../.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# Workload Identityに必要なAPIを有効にする
gcloud services enable iamcredentials.googleapis.com --project ${PROJECT_ID}

# poolを作成する
gcloud iam workload-identity-pools create ${PROJECT_ID} --project=${PROJECT_ID} --location="global" --display-name=${PROJECT_ID}

# 作成したpoolのIDを保存
export WORKLOAD_IDENTITY_POOL_ID=$(gcloud iam workload-identity-pools describe ${PROJECT_ID} --project=${PROJECT_ID} --location="global" --format="value(name)")

# サービスアカウントを作成する
gcloud iam workload-identity-pools providers create-oidc ${PROJECT_ID} \
    --project=${PROJECT_ID} \
    --location="global" \
    --workload-identity-pool=${PROJECT_ID} \
    --display-name=${PROJECT_ID} \
    --attribute-mapping="google.subject=assertion.sub,attribute.actor=assertion.actor,attribute.repository=assertion.repository" \
    --issuer-uri="https://token.actions.githubusercontent.com"

# poolに対してサービスアカウントを紐付ける
gcloud iam service-accounts add-iam-policy-binding "my-service-account@${PROJECT_ID}.iam.gserviceaccount.com" \
  --project="${PROJECT_ID}" \
  --role="roles/iam.workloadIdentityUser" \
  --member="principalSet://iam.googleapis.com/${WORKLOAD_IDENTITY_POOL_ID}/attribute.repository/${REPO}"
