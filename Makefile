.PHONY: generate-api-docs
generate-api-docs:
	 php artisan l5-swagger:generate

help:
	@grep -E '^[a-zA-Z._-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
