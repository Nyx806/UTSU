.PHONY: build push

# Variables
DOCKER_IMAGE = nyx806/utsu
DOCKER_TAG ?= latest

# Build the Docker image
build:
	docker build -t $(DOCKER_IMAGE):$(DOCKER_TAG) .

# Push the Docker image to Docker Hub
push: build
	docker push $(DOCKER_IMAGE):$(DOCKER_TAG)

# Build and push with a specific tag
release: build push

# Help command
help:
	@echo "Available commands:"
	@echo "  make build        - Build the Docker image"
	@echo "  make push         - Build and push the Docker image"
	@echo "  make release      - Build and push the Docker image"
	@echo ""
	@echo "Usage with custom tag:"
	@echo "  make build DOCKER_TAG=v1.0.0"
	@echo "  make push DOCKER_TAG=v1.0.0" 