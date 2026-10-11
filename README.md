## Rutgers IT AccessRequest Project

### Background
At Rutgers IT, professors and staff relied on manual PDF workflows for approving access to decentralized IT websites, like student GPA access and graduation eligibility. The process was inefficient: professors filed forms by hand, scanned them, and emailed them for multiple rounds of approval, leading to lost documents, no tracking, and a frustrating user experience.

To solve this, I designed and implemented a web-based workflow automation system that:
- Eliminated paper-based PDF approvals.
- Centralized requests into a single web portal.
- Added role-based dashboards for submitters, supervisors, and IT admins.
- Stored and tracked approvals with a MySQL database with a PHP backend.

The original code had a static HTML front end that fetched JSON from a REST API. As I didn't want to copy the internal team's MySQL database schema or any other details, I converted it to server-rendered PHP pages and a basic version of the MySql schema with Docker. This is a Demo of what I worked on at Rutgers IT and does not hold the full APIs, which I copied and left as reference as I made this demo.

### Key Features
Submission Portal: Users fill out requests directly on the website (replacing PDFs).
Two Dashboards:
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
make test    # PHPUnit unit tests (validation and approval rules), no database needed
```

On first start MySQL loads `db/schema.sql` (tables and lookup data) and `db/seed.sql` (mocked users and four sample requests, one per workflow status). MySQL is on `127.0.0.1:3307` as I have a local MySQL already. The compose project and container names are prefixed `rutgers-access`..

**Using it:** open http://localhost:8000 and sign in as a fictional user (the sign-in stands in for campus SSO and is not real authentication). Submitters can file a request and see their own list. Supervisors, role managers and IT admins see the queue for their stage and can Sign or Reject with an optional comment. A request goes supervisor, then role manager, then IT, then approved. Any step can reject it. Every action is saved in `formupdatelog` table.

**Code layout:** `lib/data.php` holds every query (prepared statements, one transaction for submitting a request) and the validation rules, `lib/auth.php` the session and CSRF helpers, `lib/workflow.php` the approval rules, `lib/view.php` the page chrome, and `src/` one file per page. `css/form.css` and `css/main.css` are the original styles, `css/app.css` adds a few small rules. `db/schema.sql` is the source of truth for tables.

`api/` and `model/` are an older layer that still targets the previous table.

### Configuration

Settings come from environment variables, or `.env` file. Copy `.env.example` to `.env` and set:

| Variable | Purpose |
|---|---|
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` (password) | MySQL connection (`DB_ROOT_PASS`, `WEB_PORT` are only used by `docker-compose.yml`) |
| `APP_ENV` | `development` enables verbose API errors; anything else hides them |

All database connections go through `db_connect()` in `config/config.php`. The original demo hardcoded demo credentials and campus hostnames in several files; those were removed, and verbose errors are now gated by `APP_ENV` instead of a server-address allowlist.



