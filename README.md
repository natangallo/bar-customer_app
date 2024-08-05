# Bar App

## Descrizione
Bar App è un'applicazione web per gestire i saldi dei clienti di un bar. I clienti possono visualizzare il loro saldo, lo storico degli acquisti e ricevere notifiche quando il credito scende sotto l'80%. Gli amministratori possono gestire i profili dei clienti, i prodotti e visualizzare lo storico delle attività.

## Struttura del Progetto
- **css/**: File CSS per lo stile.
- **js/**: File JavaScript per le funzionalità.
- **img/**: Immagini e icone.
- **views/**: File PHP per le pagine principali.
- **includes/**: File PHP per la connessione al database e le funzioni.
- **logs/**: File di log delle attività.
- **uploads/**: File CSV caricati.

## Installazione
1. Configura il database in `includes/db.php`.
2. Carica il progetto su un server web con supporto PHP e MySQL.
3. Importa il database dal file SQL fornito.

## Sicurezza
Le password degli utenti sono crittografate.

## Requisiti
- PHP 7.0 o superiore
- MySQL
- Un server web (Apache o simile)
