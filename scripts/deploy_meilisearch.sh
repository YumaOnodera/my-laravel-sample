source ./.env

PROJECT_ENV=stg
if [ $1 ]; then
PROJECT_ENV=$1
fi

cd ..
gcloud builds submit --config=cloudbuild.yaml \
    --substitutions=_PROJECT_ENV="${PROJECT_ENV}",_LOCATION="${GCP_REGION}",_REPOSITORY="${GCP_REPOSITORY}",_IMAGE=meilisearch,_DOCKER_FILE=Dockerfile_meilisearch
gcloud run deploy meilisearch \
    --port 7700 \
    --image ${GCP_REGION}-docker.pkg.dev/${APP_NAME}-${PROJECT_ENV}/${GCP_REPOSITORY}/meilisearch \
    --region ${GCP_REGION}
