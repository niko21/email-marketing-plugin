# Email Marketing Plugin

Un semplice plugin WordPress per raccogliere **Nome**, **Cognome** ed **Email**, e inviare **notifiche automatiche** agli iscritti ogni volta che pubblichi un nuovo post.

---

## 🚀 Funzionalità principali

* ✅ Form di iscrizione via shortcode `[email_marketing_form]`
* ✅ Integrazione con `wp_mail()` per l'invio delle notifiche
* ✅ Protezione da SPAM con validazione e nonce
* ✅ Pannello di amministrazione per gestione iscritti (prossimamente)
* ✅ Pronto per le traduzioni (`text-domain: email-marketing-plugin`)

---

## 🛠️ Installazione

1. Scarica o clona questo repository nella cartella `wp-content/plugins`
2. Attiva il plugin dal pannello di WordPress → **Plugin**
3. Aggiungi il form nel contenuto di una pagina o articolo con:

   ```
   [email_marketing_form]
   ```

---

## 🧩 Utilizzo

Puoi usare lo shortcode ovunque (post, pagine, widget) per mostrare il form.

Esempio HTML renderizzato:

```html
<form method="post">
  <input type="text" name="first_name" />
  <input type="text" name="last_name" />
  <input type="email" name="email" />
  <button type="submit">Iscriviti</button>
</form>
```

---

## 🔄 Aggiornamenti (versionamento Git)

Questo repository segue la [Semantic Versioning 2.0.0](https://semver.org/lang/it/):

* **MAJOR** – Cambiamenti incompatibili
* **MINOR** – Nuove funzionalità compatibili
* **PATCH** – Correzioni retrocompatibili

### Esempio di aggiornamento:

```bash
# Dopo una nuova funzionalità:
git checkout -b feature/form-validation
# Modifica del codice...
git commit -m "Aggiunge validazione lato client"
git push origin feature/form-validation

# Merge nella main dopo review, poi:
git tag -a v1.1.0 -m "Aggiunta validazione lato client"
git push origin v1.1.0
```

---

## 💡 Roadmap

* [x] Raccolta dati form (Nome, Cognome, Email)
* [x] Invio email automatico post-pubblicazione
* [ ] Backend per visualizzare iscritti
* [ ] Export CSV
* [ ] Integrazione con servizi esterni (Mailchimp, Sendinblue...)
* [ ] Invio email personalizzabile da pannello admin
* [ ] Supporto per conferma iscrizione double opt-in
* [ ] Compatibilità con block editor (Gutenberg block per form)

---

## 🤝 Contribuire

Pull request benvenute! Apri una **issue** per suggerimenti o segnalazioni.

### Linee guida:

* Crea un branch per ogni feature (`feature/nome`)
* Scrivi commit chiari (`git commit -m "Descrizione breve"`)
* Se possibile, includi test o esempi

---

## 📄 Licenza

Questo plugin è distribuito sotto [GPLv2 o successive](http://www.gnu.org/licenses/gpl-2.0.html), come richiesto dai requisiti WordPress.org.

---

## 🔗 Link utili

* [Codex Plugin WordPress (IT)](https://codex.wordpress.org/it:Scrivere_un_Plugin)
* [Documentazione ufficiale WP Plugin](https://developer.wordpress.org/plugins/)
