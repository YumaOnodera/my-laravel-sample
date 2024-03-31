source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

export PROJECT_ID=${APP_NAME}-${PROJECT_ENV}

gcloud builds submit --config=cloudbuild.yaml \
    --substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE="${APP_NAME}",_DOCKER_FILE=Dockerfile_php \
    --verbosity debug
gcloud run deploy ${APP_NAME} \
    --image ${GCP_REGION}-docker.pkg.dev/${PROJECT_ID}/${GCP_REPOSITORY}/${APP_NAME} \
    --region ${GCP_REGION} \
    --vpc-connector=${PROJECT_ID} \
    --vpc-egress=all-traffic \
    --set-env-vars DB_NAME=${DB_DATABASE}-${PROJECT_ENV} \
    --set-env-vars DB_USER=${DB_USERNAME} \
    --set-env-vars DB_PASS=${DB_PASSWORD} \
    --set-env-vars INSTANCE_CONNECTION_NAME=${DB_DATABASE}-${PROJECT_ENV} \
    --set-env-vars DB_PORT="3306" \
    --set-env-vars INSTANCE_HOST=${DB_HOST} \
    --set-env-vars DB_ROOT_CERT="certs/server-ca.pem" \
    --set-env-vars DB_CERT="certs/client-cert.pem" \
    --set-env-vars DB_KEY="certs/client-key.pem" \
    --set-env-vars PRIVATE_IP="TRUE"
