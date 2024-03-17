source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

# CloudSQLインスタンスを設置するVPCネットワークを作成する
gcloud compute networks create ${PROJECT_ID} \
    --subnet-mode=custom

# VPCネットワークにサブネットを設定する
gcloud compute networks subnets create ${PROJECT_ID} \
    --network=${PROJECT_ID} \
    --range=${GCP_VPC_SUBNET_RANGE} \
    --region=${GCP_REGION}

# VPC構築に必要なAPIを有効化する
gcloud services enable vpcaccess.googleapis.com

# VPCにアクセスするコネクタを作成する
gcloud compute networks vpc-access connectors create ${PROJECT_ID} \
    --region ${GCP_REGION} \
    --subnet ${PROJECT_ID}
