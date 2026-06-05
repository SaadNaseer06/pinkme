#!/usr/bin/env bash
# One-time cPanel server setup. Run via SSH before the first GitHub Actions deploy.
#
# Usage:
#   bash scripts/cpanel-server-init.sh
#
# Optional environment variables:
#   REPO_URL   Git remote (default: https://github.com/SaadNaseer06/PinkMe.git)
#   DEPLOY_DIR Absolute or ~ path (default: ~/public_html/pinkme)

set -euo pipefail

REPO_URL="${REPO_URL:-https://github.com/SaadNaseer06/PinkMe.git}"
DEPLOY_DIR="${DEPLOY_DIR:-$HOME/public_html/pinkme}"
BRANCH="${BRANCH:-main}"

echo "=== PinkMe cPanel server init ==="
echo "Repo:   ${REPO_URL}"
echo "Path:   ${DEPLOY_DIR}"
echo "Branch: ${BRANCH}"
echo ""

if [ -d "${DEPLOY_DIR}/.git" ]; then
  echo "Git repo already exists at ${DEPLOY_DIR}"
  cd "${DEPLOY_DIR}"
  git remote -v
  git fetch origin "${BRANCH}"
  git checkout "${BRANCH}" 2>/dev/null || git checkout -b "${BRANCH}" "origin/${BRANCH}"
  git reset --hard "origin/${BRANCH}"
else
  mkdir -p "$(dirname "${DEPLOY_DIR}")"
  git clone --branch "${BRANCH}" "${REPO_URL}" "${DEPLOY_DIR}"
  cd "${DEPLOY_DIR}"
fi

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
  echo ""
  echo "Created .env — edit DB_*, APP_URL, mail, and other production values before going live."
fi

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "=== Next steps ==="
echo "1. Edit ${DEPLOY_DIR}/.env (APP_ENV=production, APP_DEBUG=false, APP_URL, DB_*)"
echo "2. Add the GitHub Actions deploy public key to ~/.ssh/authorized_keys"
echo "3. Configure GitHub repository secrets (see CI_CD.md)"
echo "4. Push to ${BRANCH} — deployment runs automatically"
