#!/bin/bash

set -e

# Chiedi la nuova versione all'utente
read -p "Inserisci la nuova versione (es. 2.0.0): " version

# Controlla se il tag esiste già
if git rev-parse "v$version" >/dev/null 2>&1; then
    echo "❌ ERRORE: Il tag v$version esiste già. Scegli una versione diversa."
    exit 1
fichmod +x scripts/release.sh


# Aggiorna package.json senza creare tag git
npm version $version --no-git-tag-version

# Genera changelog da commit recenti (ultimi 10 commit come esempio)
# echo -e "\n## [$version] - $(date +%Y-%m-%d)\n### Cambiamenti recenti" >> CHANGELOG.md

git log -n 10 --pretty=format:"- %s" >> CHANGELOG.md

# Esegui commit, tag e push
git add .
git commit -m "Rilascia versione $version"
git tag -a "v$version" -m "Versione $version"
git push origin main
git push origin "v$version"

echo "✅ Versione $version rilasciata con changelog aggiornato."
