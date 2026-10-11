## Rutgers IT AccessRequest Project

### Background
At Rutgers IT, professors and staff relied on manual PDF workflows for approving access to decentralized IT websites, like student GPA access and graduation eligibility. The process was inefficient: professors filed forms by hand, scanned them, and emailed them for multiple rounds of approval, leading to lost documents, no tracking, and a frustrating user experience.

To solve this, I designed and implemented a web-based workflow automation system that:
- Eliminated paper-based PDF approvals.
- Centralized requests into a single web portal.
- Added role-based dashboards for submitters, supervisors, and IT admins.
- Stored and tracked approvals with a MySQL database with a PHP backend.

### Key Features
Submission Portal – Users fill out requests directly on the website (replacing PDFs).
Two Dashboards –
Submitter Dashboard: Track multiple requests, see approval status, timestamps, and history.
Supervisor Dashboard: Review/approve pending requests; also track their own submissions.

- Role-Based Access Control: Supervisors, IT staff, and deans only see what they need.
- Approval Workflow Automation: Requests flow through Supervisor → Role Manager → IT Team automatically.
- Scalable and maintainable: Built to extend for future request types (via text input instead of hardcoded dropdowns).

![Sequence Diagram HLD](https://github.com/launeg/Rutgers_Demo/blob/main/SequenceDiagramRutgersHLD.png)

### Local development

Requires Docker. No PHP or MySQL install needed.

```
make up      # copies .env.example to .env if missing, starts MySQL + PHP on http://localhost:8000
make reset   # wipes this project's DB volume and reloads schema + demo data
make down    # stops the containers
```

On first start MySQL loads `db/schema.sql` (tables and lookup data) and `db/seed.sql` (mocked users and four sample requests, one per workflow status). MySQL is on `127.0.0.1:3307` as I have a local MySQL already. The compose project and container names are prefixed `rutgers-access`..

**Current status:** `db/schema.sql` is the new source of truth and replaces the old phpMyAdmin dumps. The pages in `src/` still query the old table layout and are being rewritten against it in follow-up changes, so they do not work yet.

### Configuration

Settings come from environment variables, or `.env` file. Copy `.env.example` to `.env` and set:

| Variable | Purpose |
|---|---|
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | MySQL connection (`DB_ROOT_PASS`, `WEB_PORT` are only used by `docker-compose.yml`) |
| `APP_ENV` | `development` enables verbose API errors; anything else hides them |

All database connections go through `db_connect()` in `config/config.php`. The original demo hardcoded demo credentials and campus hostnames in several files; those were removed, and verbose errors are now gated by `APP_ENV` instead of a server-address allowlist.



