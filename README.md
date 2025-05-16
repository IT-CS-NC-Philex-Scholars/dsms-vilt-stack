# 🎓 PhilexScholar | Digital Scholarship Management Hub

<div align="center">

[![Status](https://img.shields.io/badge/status-in%20development-orange?style=for-the-badge)](https://github.com/IT-CS-NC-Philex-Scholars/philexscholar)
[![Version](https://img.shields.io/badge/version-1.0.0-blue?style=for-the-badge)](https://github.com/IT-CS-NC-Philex-Scholars/philexscholar/releases)
[![License](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE)

[![Laravel](https://img.shields.io/badge/Laravel-v10.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-v3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-Latest-805AD5?style=flat&logo=inertia)](https://inertiajs.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3.x-06B6D4?style=flat&logo=tailwindcss)](https://tailwindcss.com)
[![SQLite](https://img.shields.io/badge/Database-SQLite-003B57?style=flat&logo=sqlite)](https://www.sqlite.org/index.html)

**Transforming scholarship management for the Philex Mines community with a seamless, modern digital experience.**


</div>

---

## 🌟 Overview


PhilexScholar addresses the challenges of traditional scholarship management by providing a centralized, efficient, and user-friendly platform. Built on the robust **VILT stack (Vue.js, Inertia.js, Laravel, TailwindCSS)**, it streamlines the entire scholarship lifecycle – from application discovery and submission to administrative review, disbursement tracking, and reporting – for both students and administrators within the Philex Mines community.


## 🎯 Project Goal

Our primary goal is to **revolutionize the Philex Mines scholarship process** by:

1.  **Centralizing** all scholarship information and activities into a single, accessible platform.
2.  **Streamlining** application submission, review, and management workflows for enhanced efficiency.
3.  **Improving** transparency and communication between applicants, scholars, and administrators.
4.  **Providing** robust tools for administrators to manage programs and finances effectively.
5.  **Delivering** a modern, intuitive, and secure user experience for all stakeholders.

## ✨ Key Features

PhilexScholar is designed with dedicated features for its primary users:

### 👨‍🎓 For Students (Scholars)

*   **Effortless Application:** Intuitive, guided forms simplify the application process.
*   **Centralized Document Hub:** Easily upload, manage, and track required documents.
*   **Real-time Status Tracking:** Monitor application progress and receive timely notifications (🔔).
*   **Personalized Dashboard:** View application status, manage profile information, track payment schedules (💰), and communicate with administrators.
*   **Scholarship Discovery:** Browse and filter available scholarship opportunities.

### 👨‍💼 For Administrators

*   **Efficient Application Management:** Bulk processing tools, advanced filtering, and streamlined document verification.
*   **Automated Eligibility Checks:** Systematically verify applicant eligibility based on defined criteria.
*   **Comprehensive Financial Tools:** Manage disbursement schedules, track payments, and generate financial reports (📈).
*   **Flexible Program Configuration:** Define dynamic eligibility rules, manage scholarship cycles, and customize workflows.
*   **Data & Analytics:** Access dashboards with key metrics on applications, demographics, and program performance.
*   **Secure Communication Channel:** Communicate directly with applicants and scholars within the platform.

---

## 🏗️ System Architecture

PhilexScholar employs a modern monolithic approach using the VILT stack:

```mermaid
graph TD
    subgraph "Browser"
        A[Vue.js Frontend (UI/UX)]
    end
    subgraph "Server"
        B(Inertia.js Adapter)
        C[Laravel Backend (API/Logic)]
        D[(SQLite Database)]
        E[(File Storage)]
        F{Authentication (Sanctum)}
    end

    A <--> |Inertia.js| B
    B -- Requests/Props --> C
    C -- CRUD Ops --> D
    C -- Reads/Writes --> E
    C -- Manages Auth --> F
```

*   **Vue.js:** Provides a reactive and interactive user interface.
*   **Inertia.js:** Connects the Laravel backend and Vue.js frontend without building a separate API, enabling rapid development.
*   **Laravel:** Handles business logic, data management, authentication, and server-side rendering aspects.
*   **SQLite:** Offers a lightweight, file-based database suitable for this application's scale (can be swapped for MySQL/PostgreSQL if needed).
*   **File Storage:** Securely manages uploaded documents (e.g., local, S3).
*   **Laravel Sanctum:** Provides simple and effective authentication.

---

## 🛠️ Tech Stack

<details>
<summary><strong>Core Technologies Used</strong> (Click to expand)</summary>

| Category  | Technology                                                     | Description                                   |
| :-------- | :------------------------------------------------------------- | :-------------------------------------------- |
| Frontend  | **Vue.js 3** (Composition API)                                 | Progressive JavaScript framework              |
|           | **Inertia.js**                                                 | Modern monolith framework                     |
|           | **TailwindCSS 3**                                              | Utility-first CSS framework                   |
|           | `@iconify/vue` / `lucide-vue-next`                             | Icon libraries                                |
| Backend   | **Laravel 10.x**                                               | PHP Web Framework                             |
|           | **PHP 8.1+**                                                   | Server-side scripting language                |
| Database  | **SQLite 3**                                                   | Default relational database                   |
| Auth      | **Laravel Sanctum**                                            | API / Session Authentication                  |
| Dev Tools | **Vite**                                                       | Frontend tooling (dev server, bundling)       |
|           | **Composer**                                                   | PHP dependency manager                        |
|           | **NPM / Yarn**                                                 | Node.js dependency manager                    |
| UI Lib    | **Shadcn UI (Vue Port)**                                       | Re-usable UI components                       |

</details>

---

## 🗺️ Project Roadmap

This roadmap outlines our planned features and development milestones. Status updates reflect current progress.

| Feature / Milestone                 | Description                                                                   | Status        | Target / ETA |
| :---------------------------------- | :---------------------------------------------------------------------------- | :------------ | :----------- |
| **Phase 1: Foundation**             | **Setup core project structure & authentication**                             | ✅ Completed   | Q1 2024      |
| User Authentication & Profiles    | Basic registration, login, profile management (Jetstream/Breeze based)      | ✅ Completed   | Q1 2024      |
| Basic Admin User Management       | Ability for admins to manage user accounts                                    | ✅ Completed   | Q1 2024      |
| Core Database Schema              | Initial design for Scholars, Applications, Payments, Documents tables         | ✅ Completed   | Q1 2024      |
| **Phase 2: Core Features (MVP)**    | **Implement essential student and admin workflows**                           | ⏳ In Progress | Q2-Q3 2025   |
| Student Application Portal        | Step-by-step application form submission                                      | ⏳ In Progress | Q2 2025      |
| Document Upload System            | Secure file uploads for required application documents                        | ⏳ In Progress | Q2 2025      |
| Basic Admin Application Review    | View submitted applications and documents                                       | ⏳ In Progress | Q3 2025      |
| Application Status Tracking       | Students can view basic status (Submitted, Under Review)                      | 📅 Planned     | Q3 2025      |
| Basic Notification System         | Email notifications for key events (e.g., submission confirmation)            | 📅 Planned     | Q3 2025      |
| **Phase 3: Enhancements**           | **Add advanced features and improve usability**                               | 📅 Planned     | Q4 2025      |
| Advanced Admin Review Tools       | Bulk actions, filtering, sorting, document verification flags               | 📅 Planned     | Q4 2024      |
| Automated Eligibility Checks      | System checks based on predefined criteria                                    | 📅 Planned     | Q4 2024      |
| Financial Management (Admin)      | Track disbursements, manage payment schedules                                 | 📅 Planned     | Q4 2024      |
| Scholar Dashboard Enhancements    | Display payment schedule, improved status details                             | 📅 Planned     | Q4 2024      |
| Program Configuration (Admin)     | Manage scholarship details, cycles, eligibility rules                         | 💡 Idea        | 2025         |
| **Phase 4: Future Development**   | **Long-term goals and potential additions**                                   | 💡 Idea        | 2025+        |
| Reporting & Analytics             | Generate reports on applications, financials, demographics                    | 💡 Idea        | 2025         |
| Direct Communication Module       | In-app messaging between admins and applicants/scholars                     | 💡 Idea        | 2025         |
| API for Integrations              | Potential API for connecting with other Philex systems                      | 💡 Idea        | 2025+        |
| Mobile Responsiveness++           | Enhanced mobile-specific views and potentially a PWA                          | 💡 Idea        | 2025+        |

**Status Key:** ✅ Completed | ⏳ In Progress | 📅 Planned | 💡 Idea / Backlog

---

## 🚀 Getting Started

Follow these instructions to set up the PhilexScholar project for development and production environments.

### Prerequisites

* PHP >= 8.3
* Composer
* Node.js & NPM (or Yarn/Bun)
* SQLite 3 (or another database of your choice)
* Docker & Docker Compose (optional, for containerized setup)

### Recommended Installation

The quickest way to set up PhilexScholar is using the included setup script:

```bash
git clone [Your Repository Link Here] philexscholar
cd philexscholar
composer run-script setup
```

This script will:
- Install Composer dependencies
- Generate application key
- Create SQLite database
- Run migrations and seed the database
- Install Node dependencies (using Bun)
- Build frontend assets
- Generate API documentation

After running the setup script, start the development servers:
```bash
composer run-script dev
```

### Setting Up Filament Shield & Admin Access

After installation, you need to set up Filament Shield permissions and create a super admin account:

1. **Generate Shield permissions:**
   ```bash
   php artisan shield:generate --all
   ```

2. **Create a super admin account:**
   ```bash
   php artisan shield:super-admin
   ```
   
   When prompted, select option `1` to create a new super admin user.

### Alternative Installation Options

<details>
<summary><strong>Option 1: Traditional Setup</strong></summary>

1. **Clone the repository:**
   ```bash
   git clone [Your Repository Link Here] philexscholar
   cd philexscholar
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   # or
   yarn install
   # or
   bun install
   ```

4. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Update the `.env` file with your database credentials and other necessary configurations.

5. **Setup Database:**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Run migrations and seed the database
   php artisan migrate --seed
   ```

6. **Build Frontend Assets:**
   ```bash
   npm run build
   # or
   yarn build
   # or
   bun run build
   ```

7. **Start Development Servers:**
   ```bash
   # Using the included development script
   composer run-script dev
   
   # Or manually start the servers
   php artisan serve
   npm run dev
   ```

8. **Access the Application:** 
   Open your browser and navigate to http://localhost:8000
</details>

<details>
<summary><strong>Option 2: Laravel Sail (Docker)</strong></summary>

[Laravel Sail](https://laravel.com/docs/11.x/sail) provides a lightweight command-line interface for interacting with Laravel's Docker environment.

1. **Clone the repository:**
   ```bash
   git clone [Your Repository Link Here] philexscholar
   cd philexscholar
   ```

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Start Sail:**
   ```bash
   # If you have PHP installed locally
   ./vendor/bin/sail up -d
   
   # If you don't have PHP installed locally
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v $(pwd):/var/www/html \
       -w /var/www/html \
       laravelsail/php83-composer:latest \
       composer install --ignore-platform-reqs
   
   ./vendor/bin/sail up -d
   ```

4. **Generate application key:**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

5. **Run migrations and seed the database:**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

6. **Install and build frontend assets:**
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

7. **Access the application:**
   Open your browser and navigate to http://localhost

8. **Stop Sail when finished:**
   ```bash
   ./vendor/bin/sail down
   ```
</details>

<details>
<summary><strong>Option 3: Docker Compose</strong></summary>

1. **Clone the repository:**
   ```bash
   git clone [Your Repository Link Here] philexscholar
   cd philexscholar
   ```

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Update environment variables:**
   Set appropriate values in your `.env` file, particularly:
   ```
   APP_PORT=80
   VITE_PORT=5173
   WWWUSER=$(id -u)
   WWWGROUP=$(id -g)
   ```

4. **Start the Docker containers:**
   ```bash
   docker-compose up -d
   ```

5. **Install dependencies inside the container:**
   ```bash
   docker-compose exec laravel.test composer install
   docker-compose exec laravel.test npm install
   ```

6. **Generate application key:**
   ```bash
   docker-compose exec laravel.test php artisan key:generate
   ```

7. **Setup database:**
   ```bash
   docker-compose exec laravel.test touch database/database.sqlite
   docker-compose exec laravel.test php artisan migrate --seed
   ```

8. **Build frontend assets:**
   ```bash
   docker-compose exec laravel.test npm run build
   ```

9. **Access the application:**
   Open your browser and navigate to http://localhost

10. **Stop containers when finished:**
    ```bash
    docker-compose down
    ```
</details>

<details>
<summary><strong>Option 4: Quick Setup Script</strong></summary>

The project includes a setup script that handles most of the configuration automatically:

```bash
git clone [Your Repository Link Here] philexscholar
cd philexscholar
composer run-script setup
```

This script will:
- Install Composer dependencies
- Generate application key
- Create SQLite database
- Run migrations and seed the database
- Install Node dependencies
- Build frontend assets
- Generate API documentation

After running the setup script, start the development servers:
```bash
composer run-script dev
```
</details>

### Post-Installation Configuration

1. **Configure Mail:**
   Update your `.env` file with appropriate mail settings:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```
   
   When using Sail or Docker, the included Mailpit service is available at http://localhost:8025 for email testing.

2. **Configure Additional Services:**
   Update your `.env` file with configurations for any third-party services you want to use:
   - Sentry for error tracking
   - Cashier for payments
   - Socialite for OAuth authentication

### Development Workflow

1. **Start all development services:**
   ```bash
   # Using the included script
   composer run-script dev
   
   # Or with Sail
   ./vendor/bin/sail up
   ./vendor/bin/sail npm run dev
   ```

2. **Code Formatting and Analysis:**
   ```bash
   # Format code
   composer run-script format
   
   # Static analysis
   composer run-script analyse
   ```

3. **Running Tests:**
   ```bash
   composer run-script test
   ```

4. **Documentation Generation:**
   ```bash
   php artisan scribe:generate
   ```
   
   Access the API documentation at http://localhost:8000/docs

---

## ✨ Screenshots (Placeholder)

*(Add screenshots of the application interface here once available to provide a visual preview.)*

*   [Screenshot of Dashboard]
*   [Screenshot of Application Form]
*   [Screenshot of Admin Review Page]

---

## 📚 Documentation

Detailed documentation is crucial for users and developers.

*   **User Guides:** (Planned / Under Development)
    *   Scholar's Guide (`docs/scholar-guide.md`)
    *   Administrator's Manual (`docs/admin-guide.md`)
*   **Development:**
    *   API Documentation (if applicable) (`docs/api-docs.md`)
    *   Development Guide (`docs/dev-guide.md`) - *Covers coding standards, architecture details, etc.*

*(Note: Update links above or state status clearly if documentation is hosted elsewhere or not yet complete).*

---

## 🤝 Contributing

We welcome contributions from the community! Whether it's bug reporting, feature suggestions, or code contributions, your help is appreciated.

1.  Please read our [Contributing Guidelines](CONTRIBUTING.md) before starting.
2.  Check the [Issues](https://github.com/IT-CS-NC-Philex-Scholars/philexscholar/issues) tab for existing bugs or feature requests.
3.  Fork the repository, create your feature branch (`git checkout -b feature/AmazingFeature`), commit your changes, and open a Pull Request.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

<div align="center">
  <p>Built with ❤️ by the Philex Mines Technology Team</p>
  <p>
    <a href="https://github.com/IT-CS-NC-Philex-Scholars/philexscholar">GitHub Repository</a> ·
    <a href="[Link to Deployed App if applicable]">Website</a> ·
    <a href="mailto:support@philexscholar.com">Contact Support</a>
  </p>
</div>
```

**Key Improvements:**

1.  **Enhanced Badges:** Used `style=for-the-badge` for larger, more prominent status badges. Added logos to tech stack badges for better visual recognition. Linked badges to relevant places (repo, releases, license).
2.  **Compelling Overview:** Starts by stating the problem and positioning PhilexScholar as the solution. Clearly mentions the target audience.
3.  **Dedicated Project Goal:** Explicitly lists the key objectives of the project.
4.  **Improved Key Features:** Uses clear subheadings and bullet points with emojis for better readability and visual appeal compared to nested `<details>`. Descriptions are slightly more action-oriented.
5.  **Architecture Explanation:** Added brief text explaining the role of each component in the VILT stack alongside the Mermaid diagram.
6.  **Tech Stack Table:** Presented the tech stack in a more structured table format within the `<details>` tag for clarity.
7.  **Visual Roadmap:** Added a detailed roadmap table with columns for Feature/Milestone, Description, Status (using emojis ✅⏳📅💡), and Target ETA. This provides a clear view of progress and future plans.
8.  **Clearer Getting Started:** Numbered steps, clarified database setup (including creating the SQLite file), added the `npm run build` step, and mentioned accessing the app. Added placeholder for the repository link.
9.  **Screenshots Placeholder:** Added a dedicated section to indicate where screenshots should go, making the README feel more complete even without them.
10. **Documentation Status:** Made it clearer that documentation might be planned or under development and suggested updating links.
11. **Contributing Section:** Slightly more inviting tone.
12. **Footer Links:** Updated links and added placeholders for the repository and deployed app.
13. **Formatting:** Improved use of headings, horizontal rules (`---`), code blocks, and overall structure for better readability.
