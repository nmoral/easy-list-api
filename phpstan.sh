#!/usr/bin/env bash

vendor/bin/phpstan analyse --configuration=phpstan.neon --level=7 --memory-limit=-1 public src tests