# Vigaspecialweek

## 📥 Scaricare il Progetto da GitHub

### Clonare il Repository

```bash
git clone https://github.com/carmelocastiglione/vigaspecialweek.git
cd vigaspecialweek
```

## 🛠️ Prerequisiti Software

Prima di iniziare, assicurati di avere installato:

### Obbligatori

- **PHP 8.5+** ([Scarica da php.new](https://php.new/install))
  ```bash
  php -v
  ```

- **Composer** (Gestore dipendenze PHP)
  ```bash
  composer -V
  ```
  Se non installato: Eseguirai automaticamente durante l'installazione di PHP

- **Node.js 18+** e **npm** (per il frontend)
  ```bash
  node -v
  npm -v
  ```
  [Scarica da nodejs.org](https://nodejs.org)

### Consigliati

- **Git** - Per il controllo di versione
  ```bash
  git --version
  ```

- **Laravel CLI** - Per comandi Artisan
  ```bash
  laravel --version
  ```

## 🚀 Avviare il Progetto

### 1. Installare le Dipendenze

```bash
# Dipendenze PHP
composer install

# Dipendenze Frontend
npm install
```

### 2. Configurare l'Ambiente

```bash
# Copiare il file di configurazione
cp .env.example .env

# Generare la chiave dell'applicazione
php artisan key:generate
```

### 3. Preparare il Database

```bash
# Eseguire le migrazioni
php artisan migrate

# (Opzionale) Popolare il database con dati di prova
php artisan db:seed
```

### 4. Avviare l'Applicazione

Apri **due terminali** separati:

**Terminal 1 - Server Backend:**
```bash
composer run dev
```
L'applicazione sarà disponibile su `http://localhost:8000`

**Terminal 2 - Compilazione Frontend:**
```bash
npm run dev
```

Per il deployment di produzione:
```bash
npm run build
```

## 🐳 Docker Compose (Sviluppo Locale)

L'applicazione include una configurazione **Docker Compose** per facilitare la gestione del database di sviluppo locale.

### Prerequisiti Docker

- **Docker Desktop** ([Scarica da docker.com](https://www.docker.com/products/docker-desktop))
  ```bash
  docker --version
  docker compose --version
  ```

### 1. Avviare i Servizi con Docker Compose

```bash
# Avviare i container (database e altri servizi)
docker compose up -d

# Visualizzare lo stato dei container
docker compose ps
```

### 2. Eseguire le Migrazioni

```bash
# Con il database in Docker
php artisan migrate

# Opzionale: Popolare con dati di prova
php artisan db:seed
```

## �📝 Proporre Pull Request

### 1. Creare un Branch Locale

Usa un nome descrittivo per il branch:

```bash
git checkout -b feature/aggiungi-notifiche
# oppure
git checkout -b fix/correggi-validazione-email
# oppure
git checkout -b docs/aggiorna-readme
```

**Convenzioni di nomenclatura:**
- `feature/` - Nuove funzionalità
- `fix/` - Correzioni di bug
- `docs/` - Documentazione
- `refactor/` - Refactoring del codice
- `test/` - Aggiunta/miglioramento test

### 2. Effettuare le Modifiche

```bash
# Verificare i file modificati
git status

# Aggiungere i file da committare
git add .

# Committare con messaggio descrittivo
git commit -m "Feat: aggiungi sistema di notifiche email"
```

**Linee Guida per i Commit:**
- Usare l'imperative mood: "Aggiungi" non "Aggiunto"
- Essere specifico e conciso
- Se il commit risolve un issue, aggiungere il numero: `Fix #123: descrizione`
- Separare il titolo dal corpo con una riga vuota (se necessario)

### 3. Formattare il Codice

Prima di pushare, assicurati che il codice sia formattato correttamente:

```bash
vendor/bin/pint
npm run format  # se applicabile
```

### 4. Pushare il Branch

```bash
git push origin feature/aggiungi-notifiche
```

### 5. Aprire la Pull Request su GitHub

1. Vai su [GitHub Repository](https://github.com/carmelocastiglione/vigaspecialweek)
2. Clicca su **"Pull requests"**
3. Clicca su **"New pull request"**
4. Seleziona il tuo branch come source
5. Compila il template della PR:

```markdown
## 📝 Descrizione
Breve descrizione delle modifiche.

## 🔗 Link
- Risolve #123 (se applicabile)

## ✅ Checklist
- [ ] Ho eseguito i test locali
- [ ] Ho formattato il codice con Pint
- [ ] Ho aggiunto/aggiornato i test
- [ ] La documentazione è aggiornata
- [ ] Ho revisionato il mio stesso codice
```

### 6. Rispondere ai Commenti dei Reviewer

- Rispondi a tutti i commenti
- Fai i cambiamenti richiesti
- Committa e pusha i cambiamenti (non fare force push)

```bash
git add .
git commit -m "Review: indirizza commenti sulla validazione"
git push origin feature/aggiungi-notifiche
```

## 🤝 Linee Guida di Contribuzione

- Seguire gli standard di codice Laravel e PHP 8.5
- Aggiornare la documentazione se necessario
- Essere rispettoso nei commenti e nelle discussioni
- Una PR per feature/fix (evitare PR troppo grandi)

## 📧 Contatti

Per domande o suggerimenti, apri un [Issue](https://github.com/carmelocastiglione/vigaspecialweek/issues).

---

**Buon coding! 🎉**