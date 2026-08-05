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
- Beautiful, animated hero section
- Feature showcase with hover effects
- Responsive design for all devices
- Pure white background with blue accents
- Smooth transitions and animations

### 📂 **Presentation Management**
- Upload PDF/PowerPoint presentations
- Organize presentations by categories
- Search and filter functionality
- Iframe-based presentation viewer
- Sidebar navigation with file list

### 🔐 **Authentication System**
- User registration and login
- Password reset functionality
- Profile management
- Secure password validation
- Session management

### 👤 **User Dashboard**
- Personal presentation library
- Upload and manage content
- Settings and profile customization
- Activity tracking

### 🎯 **Responsive Design**
- Mobile-first approach
- Tablet and desktop optimized
- Touch-friendly interface
- Fast page loads with SSR

---

## 🛠️ Tech Stack

### Backend
- **Laravel 11.x** - PHP Framework
- **Laravel Fortify** - Authentication scaffolding
- **MySQL** - Database
- **PHP 8.2+** - Programming language

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith approach with SSR
- **TypeScript** - Type-safe JavaScript
- **TailwindCSS** - Utility-first CSS framework
- **shadcn-vue** - Re-usable component library
- **Lucide Icons** - Beautiful icon library
- **Vite** - Fast build tool

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

Run migrations:

```bash
php artisan migrate
```

### 6. Storage Link

```bash
php artisan storage:link
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
2. **No Registration Required** - Access all reading materials and videos freely
3. **Optional Account** - Create an account to track your progress (optional)

### For Educators/Admins

1. **Register/Login** - Create an account or log in
2. **Upload Content** - Navigate to Dashboard → Upload
3. **Manage Presentations** - View, edit, or delete uploaded materials
4. **Organize Content** - Categorize presentations for easy discovery

---

## 📁 Project Structure

```
gramatecha/
├── app/
│   ├── Actions/          # Custom action classes
│   ├── Http/
│   │   ├── Controllers/  # Application controllers
│   │   ├── Middleware/   # Custom middleware
│   │   └── Requests/     # Form request validation
│   └── Models/           # Eloquent models
├── database/
│   └── migrations/       # Database migrations
├── resources/
│   ├── js/
│   │   ├── components/   # Vue components
│   │   │   ├── landing/  # Landing page components
│   │   │   └── ui/       # Reusable UI components
│   │   ├── layouts/      # Layout components
│   │   └── pages/        # Inertia pages
│   └── views/            # Blade templates
├── routes/
│   └── web.php           # Web routes
└── tests/                # Application tests
```

---

## 🎨 Key Components

### Landing Page
- **Hero.vue** - Main hero section with animated elements
- **Features.vue** - Feature showcase with card animations
- **Footer.vue** - Footer with navigation links

### Presentation System
- **PresentationCard.vue** - Individual presentation card component
- **PresentationLayout.vue** - Layout for presentation pages
- **TopNav.vue** - Top navigation bar

### UI Components
- **Button, Card, Badge** - shadcn-vue components
- **Sidebar** - Collapsible sidebar navigation
- **ScrollArea** - Custom scrollable area

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

- [ ] Mobile app (iOS/Android)
- [ ] Offline mode support
- [ ] Interactive quizzes and assessments
- [ ] Progress tracking and analytics
- [ ] Community forum
- [ ] Teacher-student collaboration tools
- [ ] Live video lessons
- [ ] Gamification features

---

<div align="center">

**Made with ❤️ for Filipino Students**

*Matuto. Maintindihan. Makipag-usap.*

</div>
