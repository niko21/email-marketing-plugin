#!/bin/bash

set -e

# Chiedi la nuova versione all'utente
read -p "Inserisci la nuova versione (es. 2.0.0): " version

# Controlla se il tag esiste già
if git rev-parse "v$version" >/dev/null 2>&1; then
  echo "❌ ERRORE: Il tag v$version esiste già. Scegli una versione diversa."
  exit 1
fi

# Aggiorna package.json senza creare tag git
npm version $version --no-git-tag-version

# Genera changelog da commit recenti (ultimi 10 commit come esempio)
echo -e "\n## [$version] - $(date +%Y-%m-%d)\n### Cambiamenti recenti" >> CHANGELOG.md
git log -n 10 --pretty=format:"- %s" >> CHANGELOG.md

# Aggiungi, committa e crea tag se non esiste
git add .
git commit -m "Rilascia versione $version"

# Verifica se il tag è stato creato, se no lo crea
if ! git rev-parse "v$version" >/dev/null 2>&1; then
  git tag -a "v$version" -m "Versione $version"
fi

# Push branch e tag
git push origin main
git push origin "v$version"

echo "✅ Versione $version rilasciata con changelog aggiornato."