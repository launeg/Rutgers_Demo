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



