#!/bin/bash
# Add GitHub Secrets for BioTree Deployment
# Run this script with your GitHub token

# Check if GitHub CLI is available
if command -v gh &> /dev/null; then
    echo "Using GitHub CLI..."
    gh secret set DEPLOY_HOST --body "217.15.167.23"
    gh secret set DEPLOY_USER --body "root"
    gh secret set DEPLOY_PASSWORD --body "3genius8!Q!Q"
    gh secret set DEPLOY_PORT --body "22"
else
    echo "GitHub CLI not found. Please add secrets manually:"
    echo ""
    echo "Go to: https://github.com/kerabatdigital/biotree/settings/secrets/actions"
    echo ""
    echo "Add these secrets:"
    echo "  DEPLOY_HOST = 217.15.167.23"
    echo "  DEPLOY_USER = root"
    echo "  DEPLOY_PASSWORD = 3genius8!Q!Q"
    echo "  DEPLOY_PORT = 22"
fi
