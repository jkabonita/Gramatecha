# 📚 GRAMATECHA - Pasulat at Pasalitang Komunikasyon

<div align="center">

![GRAMATECHA](https://img.shields.io/badge/GRAMATECHA-Filipino%20Learning%20Platform-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green?style=for-the-badge&logo=vue.js)
![Inertia.js](https://img.shields.io/badge/Inertia.js-SSR-purple?style=for-the-badge)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue?style=for-the-badge&logo=tailwindcss)

**Free E-Learning Platform for Filipino Students**

[Features](#-features) • [Tech Stack](#-tech-stack) • [Installation](#-installation) • [Usage](#-usage) • [Contributing](#-contributing)

</div>

---

## 🌟 About

**GRAMATECHA** is a free, modern learning platform dedicated to **Pasulat at Pasalitang Komunikasyon** (Written and Oral Communication in Filipino). Built specifically for Senior High School and College students, GRAMATECHA provides accessible reading materials and video lessons grounded in the standards of the **Komisyon sa Wikang Filipino (KWF)**.

### Why GRAMATECHA?

- 📖 **Free & Accessible** - No registration fees, no hidden costs
- 🎓 **KWF Standards-Based** - All content aligned with official guidelines
- 🎥 **Multi-Format Learning** - Reading materials and video lessons
- 🇵🇭 **For Filipinos, By Filipinos** - Modern, clear, relevant content
- ⚡ **Modern Platform** - Beautiful UI with smooth animations
- 🔒 **Secure** - Built with Laravel Fortify for authentication

---

## ✨ Features

### 🎨 **Modern Landing Page**
- Beautiful, animated hero section with scroll reveal effects
- Feature showcase with hover effects
- Responsive design for all devices
- Pure white background with blue accents
- Smooth transitions and animations
- About page with team member profiles
- FAQ section for common questions

### 📂 **Presentation Management**
- Upload PDF/PowerPoint/Video presentations
- Support for multiple file formats (PDF, PPTX, MP4, MOV, AVI, etc.)
- Bulk upload functionality for multiple files
- Organize presentations by hierarchical categories
- Advanced search and filter functionality
- Multiple viewer types:
  - PDF page-by-page viewer
  - PowerPoint slide viewer with PHPPresentation
  - Video streaming with optimized delivery
- Sidebar navigation with file list
- View count tracking
- Presentation visibility controls (published/draft)

### 🎥 **Video Streaming**
- Optimized video streaming with range requests
- Support for multiple video formats
- Multiple video player options:
  - Free video player with native controls
  - Plyr.js enhanced player
  - Optimized streaming player
- Bandwidth-efficient delivery
- Responsive video players
- Video compression utilities

### 📝 **Interactive Quizzes**
- Create and manage quizzes
- Multiple choice questions
- Student name tracking (no login required)
- Timed quizzes with countdown
- Instant feedback and scoring
- Quiz results display with correct/incorrect answers
- Quiz attempt history
- Admin dashboard for quiz attempts
- Detailed attempt analytics per student

### 🏷️ **Category System**
- Hierarchical category management
- Parent-child category relationships
- Category-based content organization
- Category filtering and navigation
- Admin category CRUD operations

### 👨‍💼 **Admin Panel**
- Complete admin dashboard
- Presentation management:
  - Create, edit, delete presentations
  - Bulk upload interface
  - Upload diagnostics and troubleshooting
  - Preview presentations before publishing
- Category management interface
- Quiz management and monitoring
- Student attempt analytics
- File upload diagnostics
- Admin-only middleware protection

### 🔐 **Authentication System**
- User registration and login with Laravel Fortify
- Password reset functionality
- Profile management
- Secure password validation
- Session management
- Admin role management
- Guest quiz access (no login required for students)

### 👤 **User Dashboard**
- Personal presentation library
- Upload and manage content
- Settings and profile customization
- Activity tracking
- Role-based access control

### 🎯 **Responsive Design**
- Mobile-first approach
- Tablet and desktop optimized
- Touch-friendly interface
- Fast page loads with SSR (Server-Side Rendering)
- Progressive enhancement
- Accessible components

---

## 🛠️ Tech Stack

### Backend
- **Laravel 11.x** - PHP Framework
- **Laravel Fortify** - Authentication scaffolding
- **MySQL** - Database
- **PHP 8.2+** - Programming language
- **PHPPresentation** - PowerPoint file processing
- **PHPOffice** - Office document manipulation

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith approach with SSR
- **TypeScript** - Type-safe JavaScript
- **TailwindCSS** - Utility-first CSS framework
- **shadcn-vue** - Re-usable component library
- **Lucide Icons** - Beautiful icon library
- **Radix Vue** - Unstyled, accessible components
- **Vite** - Fast build tool
- **PDF.js** - PDF rendering in browser
- **Plyr.js** - HTML5 video player

### Development Tools
- **Pest** - Testing framework
- **Prettier** - Code formatter
- **ESLint** - JavaScript linter
- **Laravel Pint** - PHP code style fixer

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** or **yarn**
- **MySQL** >= 8.0 or **MariaDB** >= 10.3

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/gramatecha.git
cd gramatecha
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
# or
yarn install
```

### 4. Environment Configuration

```bash
# Copy the example environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gramatecha
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations and seed the database:

```bash
php artisan migrate --seed
```

This will create:
- Categories table with sample categories
- Presentations table
- Quizzes, quiz questions, and quiz attempts tables
- Admin user (check seeder for credentials)

### 6. Storage Setup

Create required storage directories and link:

```bash
php artisan storage:link

# Ensure presentations directory exists
mkdir public\presentations
```

### 7. Build Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 8. Start the Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## 🎯 Usage

### For Students

1. **Browse Lessons** - Visit the landing page to explore available content
2. **View Presentations** - Click on any presentation to view PDFs, PowerPoints, or videos
3. **Take Quizzes** - Navigate to Quizzes section and test your knowledge
4. **No Registration Required** - Access all reading materials, videos, and quizzes freely
5. **Track Progress** - Enter your name to save quiz results
6. **Optional Account** - Create an account to upload and share content (optional)

### For Educators/Admins

1. **Access Admin Panel** - Login with admin credentials
2. **Manage Categories** - Create hierarchical categories for organizing content
3. **Upload Content**:
   - Single presentation upload (PDF, PPTX, or Video)
   - Bulk upload multiple files at once
   - Set category, visibility, and metadata
4. **Create Quizzes**:
   - Add multiple choice questions
   - Set quiz title and description
   - Publish for students
5. **Monitor Activity**:
   - View quiz attempts and scores
   - Track presentation views
   - Analyze student performance
6. **Manage Content** - View, edit, or delete uploaded materials
7. **Troubleshoot Uploads** - Use upload diagnostic tools for issues

---

## 📁 Project Structure

```
gramatecha/
├── app/
│   ├── Actions/          # Custom action classes
│   ├── Http/
│   │   ├── Controllers/  
│   │   │   ├── Admin/    # Admin controllers (Category, UploadDiagnostic)
│   │   │   ├── PresentationController.php
│   │   │   ├── QuizController.php
│   │   │   └── VideoStreamController.php
│   │   ├── Middleware/   # Custom middleware (EnsureIsAdmin, OptimizeStreaming)
│   │   └── Requests/     # Form request validation
│   ├── Jobs/             # Async jobs (ProcessBulkPresentationUpload)
│   ├── Models/           # Eloquent models
│   │   ├── Category.php
│   │   ├── Presentation.php
│   │   ├── Quiz.php
│   │   ├── QuizQuestion.php
│   │   └── QuizAttempt.php
│   └── Services/         # Business logic services
│       ├── FileStorageService.php
│       ├── PHPPresentationGenerator.php
│       ├── PptxReaderService.php
│       └── PresentationService.php
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
│       ├── CategorySeeder.php
│       ├── QuizSeeder.php
│       ├── ComprehensiveQuizSeeder.php
│       └── SystemCategorySeeder.php
├── resources/
│   ├── js/
│   │   ├── components/   # Vue components
│   │   │   ├── landing/  # Landing page components
│   │   │   ├── ui/       # Reusable UI components (shadcn-vue)
│   │   │   ├── FreeVideoPlayer.vue
│   │   │   ├── PlyrVideoPlayer.vue
│   │   │   ├── PdfPageViewer.vue
│   │   │   ├── PptxSlideViewer.vue
│   │   │   └── PublicNav.vue
│   │   ├── composables/  # Vue composables
│   │   │   ├── useAppearance.ts
│   │   │   └── useScrollReveal.ts
│   │   ├── layouts/      # Layout components
│   │   │   ├── AuthLayout.vue
│   │   │   ├── PublicLayout.vue
│   │   │   └── PresentationLayout.vue
│   │   ├── pages/        # Inertia pages
│   │   │   ├── Admin/    # Admin pages
│   │   │   │   ├── Categories/
│   │   │   │   ├── Presentations/
│   │   │   │   └── Quizzes/
│   │   │   ├── Presentations/
│   │   │   ├── Quizzes/
│   │   │   ├── Videos/
│   │   │   ├── About.vue
│   │   │   ├── Faq.vue
│   │   │   └── Landing.vue
│   │   └── utils/        # Utility functions
│   └── views/            # Blade templates
├── routes/
│   ├── web.php           # Web routes
│   └── streaming.php     # Video streaming routes
├── public/
│   ├── presentations/    # Uploaded presentation files
│   ├── about/            # Team member photos
│   └── home/             # Landing page images
└── tests/                # Application tests
```

---

## 🎨 Key Components

### Landing Page
- **Hero.vue** - Main hero section with animated elements and scroll reveal
- **Features.vue** - Feature showcase with card animations
- **Footer.vue** - Footer with navigation links
- **About.vue** - Team member profiles and information
- **Faq.vue** - Frequently asked questions

### Presentation System
- **PresentationCard.vue** - Individual presentation card component
- **PresentationLayout.vue** - Layout for presentation pages
- **PdfPageViewer.vue** - Page-by-page PDF viewer with PDF.js
- **PptxSlideViewer.vue** - PowerPoint slide viewer
- **TopNav.vue** - Top navigation bar

### Video Streaming
- **FreeVideoPlayer.vue** - Native HTML5 video player
- **PlyrVideoPlayer.vue** - Enhanced video player with Plyr.js
- **OptimizedVideoPlayer.vue** - Optimized streaming player
- **VideoStreamController.php** - Backend video streaming with range support

### Quiz System
- **Quiz pages** - Index, Show, Take, Results
- **QuizController.php** - Quiz logic and scoring
- **Quiz models** - Quiz, QuizQuestion, QuizAttempt
- **Admin quiz management** - Attempt tracking and analytics

### Admin Panel
- **Admin/Categories/Index.vue** - Category management
- **Admin/Presentations/** - Presentation CRUD operations
  - Index, Create, Edit, Show, BulkCreate
- **Admin/Quizzes/** - Quiz attempt monitoring
  - Attempts, AttemptDetails
- **CategoryController.php** - Category management API
- **UploadDiagnosticController.php** - Upload troubleshooting

### UI Components
- **Button, Card, Badge** - shadcn-vue components
- **Table** - Data table with sorting
- **Progress** - Progress bars for quizzes
- **RadioGroup** - Radio button groups for quiz questions
- **Sidebar** - Collapsible sidebar navigation
- **ScrollArea** - Custom scrollable area
- **PublicNav.vue** - Public-facing navigation

---

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

---

## 🔧 Configuration

### File Upload Settings

Configure maximum file sizes in `.env`:

```env
# Maximum upload size (adjust php.ini accordingly)
UPLOAD_MAX_FILESIZE=100M
POST_MAX_SIZE=100M
```

Update `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
```

### Fortify Configuration

The authentication system uses Laravel Fortify. Configure features in `config/fortify.php`:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
],
```

### Admin Access

Create an admin user manually or via seeder:

```bash
# Run admin seeder
php artisan db:seed --class=AdminUserSeeder
```

Or create manually in database:
- Set `is_admin` = 1 in users table

### Queue Configuration

For bulk uploads and async processing:

```bash
# Run queue worker
php artisan queue:work

# Or use supervisor in production
```

### Inertia Configuration

SSR is enabled by default. Configure in `config/inertia.php` or disable SSR:

```bash
npm run dev  # Without SSR
npm run dev:ssr  # With SSR
```

---

## 🌐 Deployment

### Production Build

```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables

Ensure these are set in production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 for PHP code
- Use Prettier for JavaScript/Vue formatting
- Write tests for new features
- Update documentation as needed

---

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

---

## 👥 Authors

- **Your Name** - *Initial work* - [YourGitHub](https://github.com/yourusername)

---

## 🙏 Acknowledgments

- **Komisyon sa Wikang Filipino (KWF)** - For the language standards
- **shadcn-vue** - For the beautiful component library
- **Laravel Community** - For the amazing framework
- **Vue.js Team** - For the reactive frontend framework
- All Filipino educators and students who inspire this work

---

## 📞 Support

If you encounter any issues or have questions:

- 📧 Email: support@gramatecha.com
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/gramatecha/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/yourusername/gramatecha/discussions)

---

## 🗺️ Roadmap

### ✅ Completed
- [x] Modern landing page with animations
- [x] PDF and PowerPoint presentation viewers
- [x] Video streaming with multiple player options
- [x] Interactive quiz system with scoring
- [x] Hierarchical category management
- [x] Admin panel with full CRUD operations
- [x] Bulk upload functionality
- [x] Guest quiz access (no login required)
- [x] Quiz attempt tracking and analytics
- [x] Responsive design across all devices

### 🚧 In Progress
- [ ] Enhanced analytics dashboard
- [ ] Student progress tracking
- [ ] Content recommendation system

### 📅 Planned Features
- [ ] Mobile app (iOS/Android)
- [ ] Offline mode support
- [ ] Advanced quiz types (fill-in-the-blank, matching, essay)
- [ ] Community forum
- [ ] Teacher-student collaboration tools
- [ ] Live video lessons and webinars
- [ ] Gamification features (badges, leaderboards)
- [ ] Content bookmarking and favorites
- [ ] Study planner and calendar
- [ ] Multi-language support
- [ ] API for third-party integrations

---

<div align="center">

**Made with ❤️ for Filipino Students**

*Matuto. Maintindihan. Makipag-usap.*

</div>
