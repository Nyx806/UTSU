IMAGE_NAME := nyx806/utsu
TAG := latest

build:
	docker build -t $(IMAGE_NAME):$(TAG) .

push:
	docker push $(IMAGE_NAME):$(TAG)

pull:
	docker pull $(IMAGE_NAME):$(TAG)

.PHONY: build push pull