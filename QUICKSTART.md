# Kasflow - Quick Start Guide

## 🚀 Getting Started

### 1. Install Dependencies

```bash
# Backend
composer install

# Frontend
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_DATABASE=kasflow
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Setup Database

```bash
# Create database
mysql -u root -e "CREATE DATABASE kasflow"

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 4. Start Development Servers

```bash
# Terminal 1: Start Vite (Frontend)
npm run dev

# Terminal 2: Laravel is already running via Herd
# Access at: http://kasflow-app.test
```

## 🎯 Access the Application

### Web Interface
```
http://kasflow-app.test
```

### Demo Accounts

**Owner Account:**
- Email: `owner@example.com`
- Password: `password`
- Access: Full dashboard, reports, expense recording

**Cashier Account:**
- Email: `cashier1@example.com`
- Password: `password`
- Access: Income recording only

## 📱 Testing the Cashier Workflow

1. Login as cashier
2. You'll see today's income summary
3. Tap "Add Income" button
4. Enter amount (e.g., 50000)
5. Tap "Save Income"
6. See success animation
7. Redirected to home with updated total

**Expected time: ~5 seconds**

## 🖥️ Testing the Owner Dashboard

1. Login as owner
2. See dashboard with summary cards
3. View recent transactions
4. Access sidebar navigation
5. Switch between stores (if multiple)

## 🔧 Development Tips

### Hot Module Replacement (HMR)
Vite automatically reloads when you save Vue files.

### Debugging
```javascript
// In any Vue component
console.log(usePage().props); // See all page props
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📦 Build for Production

```bash
# Build frontend assets
npm run build

# Optimize backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🎨 Customizing the UI

### Change Colors
Edit `tailwind.config.js`:
```javascript
theme: {
    extend: {
        colors: {
            primary: '#your-color',
        },
    },
},
```

### Modify Layouts
- Owner: `resources/js/Layouts/OwnerLayout.vue`
- Cashier: `resources/js/Layouts/CashierLayout.vue`

## 🐛 Common Issues

### Issue: Vite not connecting
**Solution:**
```bash
# Kill existing Vite process
# Restart: npm run dev
```

### Issue: 419 CSRF Token Mismatch
**Solution:**
```bash
php artisan config:clear
# Clear browser cookies
```

### Issue: Inertia page not loading
**Solution:**
- Check browser console for errors
- Verify route exists in `routes/web.php`
- Check component path matches route

## 📚 Next Steps

1. ✅ Login and explore both roles
2. ✅ Record some transactions
3. ✅ Test on mobile device (responsive)
4. 📝 Create expense recording page
5. 📊 Create reports page
6. 👥 Create cashier management page

## 🔗 Useful Links

- [Laravel Docs](https://laravel.com/docs)
- [Inertia.js Docs](https://inertiajs.com)
- [Vue 3 Docs](https://vuejs.org)
- [TailwindCSS Docs](https://tailwindcss.com)

## 💡 Pro Tips

1. **Mobile Testing:** Use Chrome DevTools device emulation
2. **Fast Refresh:** Save files to see instant updates
3. **Component Inspector:** Use Vue DevTools browser extension
4. **Network Tab:** Monitor Inertia requests (look for X-Inertia header)

## 🎯 What's Implemented

✅ Authentication (Login/Register)
✅ Cashier home page with today's income
✅ Income recording with large numeric input
✅ Owner dashboard with summary stats
✅ Responsive layouts (mobile-first)
✅ Toast notifications
✅ Currency formatting
✅ Success animations
✅ Role-based layouts

## 📋 To Be Implemented

- [ ] Expense recording page
- [ ] Financial reports (daily/weekly/monthly)
- [ ] Cashier management
- [ ] Store management
- [ ] Transaction history
- [ ] Charts/graphs
- [ ] Export reports
- [ ] PWA support

## 🚀 Ready to Code!

Your development environment is ready. Start building amazing features! 🎉
