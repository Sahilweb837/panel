# Font Awesome Icons Reference

## All Icons Used in the Application

### Navigation Icons (Sidebar)

| Icon | Class | Location | Usage |
|------|-------|----------|-------|
| 📊 | `fa-chart-line` | Dashboard nav | Analytics dashboard link |
| 📚 | `fa-book` | Courses nav | Course management link |
| 👥 | `fa-users` | Students nav | Student list link |
| 👨‍🏫 | `fa-person-chalkboard` | Staff nav | Employee/staff management |
| ✓️ | `fa-clipboard-check` | Attendance nav | Attendance tracking |
| 💰 | `fa-file-invoice-dollar` | Fee Invoices nav | Invoice management |
| 💸 | `fa-money-bill-wave` | Expenses nav | Expense tracking |
| 💵 | `fa-wallet` | Salary Slips nav | Salary management |
| 🚪 | `fa-sign-out-alt` | Logout button | Sign out action |

---

### Login Page Icons

| Icon | Class | Location | Usage |
|------|-------|----------|-------|
| 🎓 | `fa-graduation-cap` | Brand mark | Company/education icon |
| 📈 | `fa-chart-line` | Feature 1 | Analytics feature |
| 👥 | `fa-users` | Feature 2 | Student management feature |
| 💰 | `fa-file-invoice-dollar` | Feature 3 | Fee management feature |
| 💼 | `fa-briefcase` | Feature 4 | Staff management feature |
| 🚨 | `fa-moon` | Theme toggle | Dark mode toggle |
| 📧 | `fa-envelope` | Email label | Email input field |
| 🔒 | `fa-lock` | Password label | Password input field |
| 👁️ | `fa-eye` | Password toggle | Show password button |
| 👁️‍🗨️ | `fa-eye-slash` | Password toggle | Hide password button |
| 👤 | `fa-user-tie` | Account type | User selection |
| 🏢 | `fa-building` | Account option | Institute/Admin option |
| 🎯 | `fa-chalkboard-user` | Account option | Staff option |
| ✉️ | `fa-at` | Email input icon | Email symbol |
| 🔐 | `fa-sign-in-alt` | Login button | Sign in action |

---

### Dashboard Icons

| Icon | Class | Location | Usage |
|------|-------|----------|-------|
| 👥 | `fa-users` | Students stat | Total students count |
| 👨‍🏫 | `fa-person-chalkboard` | Staff stat | Total staff count |
| ✓️ | `fa-clipboard-check` | Attendance stat | Total attendance records |
| 💰 | `fa-file-invoice-dollar` | Pending fees stat | Pending invoices count |
| 🕐 | `fa-clock` | Recent attendance section | Recent records header |
| 📄 | `fa-receipt` | Recent invoices section | Invoice records header |

---

### Alert & Status Icons

| Icon | Class | Location | Usage |
|------|-------|----------|-------|
| ✅ | `fa-check-circle` | Success alert | Success message icon |
| ⚠️ | `fa-exclamation-circle` | Error alert | Error message icon |

---

## How to Replace Icons

### Step 1: Find the Icon in Your Code

Look for the file and line number where the icon is used.

**Example:** Changing the Dashboard icon

**File:** `resources/views/layouts/app.blade.php`

**Current Code:**
```html
<i class="fas fa-chart-line"></i>
```

### Step 2: Choose a New Icon

1. Visit https://fontawesome.com/icons
2. Search for an icon you like
3. Copy the icon name

**Example:** Searching for "home"
- You might find: `fa-home` or `fa-house`

### Step 3: Replace the Class

```html
<!-- Old -->
<i class="fas fa-chart-line"></i>

<!-- New (example: use home icon instead) -->
<i class="fas fa-home"></i>
```

### Step 4: Save and Test

1. Save the file (Ctrl+S)
2. Refresh the browser (Ctrl+F5)
3. The icon should update immediately

---

## Popular Icon Alternatives

### Dashboard Icon Options
```html
<!-- Current -->
<i class="fas fa-chart-line"></i>

<!-- Alternatives -->
<i class="fas fa-tachometer-alt"></i>  <!-- Speedometer -->
<i class="fas fa-home"></i>             <!-- House -->
<i class="fas fa-th-large"></i>         <!-- Grid -->
<i class="fas fa-cubes"></i>            <!-- Blocks -->
```

### Student Icon Options
```html
<!-- Current -->
<i class="fas fa-users"></i>

<!-- Alternatives -->
<i class="fas fa-people-group"></i>     <!-- Group -->
<i class="fas fa-user-group"></i>       <!-- Crowd -->
<i class="fas fa-users-line"></i>       <!-- Users line -->
```

### Course Icon Options
```html
<!-- Current -->
<i class="fas fa-book"></i>

<!-- Alternatives -->
<i class="fas fa-book-open"></i>        <!-- Open book -->
<i class="fas fa-book-bookmark"></i>    <!-- Bookmark -->
<i class="fas fa-book-reader"></i>      <!-- Person reading -->
```

### Fee/Money Icon Options
```html
<!-- Current -->
<i class="fas fa-file-invoice-dollar"></i>

<!-- Alternatives -->
<i class="fas fa-credit-card"></i>      <!-- Credit card -->
<i class="fas fa-money-check"></i>      <!-- Money check -->
<i class="fas fa-coins"></i>            <!-- Coins -->
<i class="fas fa-dollar-sign"></i>      <!-- Dollar sign -->
```

### Staff Icon Options
```html
<!-- Current -->
<i class="fas fa-person-chalkboard"></i>

<!-- Alternatives -->
<i class="fas fa-chalkboard-user"></i>  <!-- User at board -->
<i class="fas fa-people-line"></i>      <!-- People line -->
<i class="fas fa-user-graduate"></i>    <!-- Graduation user -->
```

### Attendance Icon Options
```html
<!-- Current -->
<i class="fas fa-clipboard-check"></i>

<!-- Alternatives -->
<i class="fas fa-list-check"></i>       <!-- Check list -->
<i class="fas fa-check-square"></i>     <!-- Check box -->
<i class="fas fa-calendar-check"></i>   <!-- Calendar check -->
```

---

## Icon Styles

All icons use the free Font Awesome style:
- **Class Prefix:** `fas` (solid style)
- **Free Icons:** All icons listed here are available in Font Awesome Free

### Using Different Styles (Pro only)
```html
<!-- Solid (what we use) -->
<i class="fas fa-chart-line"></i>

<!-- Regular (pro) -->
<i class="far fa-chart-line"></i>

<!-- Light (pro) -->
<i class="fal fa-chart-line"></i>

<!-- Duotone (pro) -->
<i class="fad fa-chart-line"></i>
```

**Note:** Free version only includes solid (`fas`) style.

---

## Icon Sizing

### Using Icon Sizes

```html
<!-- Normal size (inherit from parent) -->
<i class="fas fa-chart-line"></i>

<!-- Larger sizes -->
<i class="fas fa-chart-line fa-lg"></i>      <!-- 33% larger -->
<i class="fas fa-chart-line fa-2x"></i>      <!-- 200% -->
<i class="fas fa-chart-line fa-3x"></i>      <!-- 300% -->
<i class="fas fa-chart-line fa-5x"></i>      <!-- 500% -->
<i class="fas fa-chart-line fa-10x"></i>     <!-- 1000% -->

<!-- Custom CSS sizing -->
<i class="fas fa-chart-line" style="font-size: 2.5rem;"></i>
```

**Used in App:**
- Navigation: `font-size: 1.1rem`
- Stat cards: `font-size: 2.5rem`
- Form labels: `font-size: 1rem`

---

## Icon Colors

### Using Colors with Icons

```html
<!-- Using CSS classes (tailored for the app) -->
<i class="fas fa-check-circle" style="color: #10b981;"></i>  <!-- Green -->
<i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>  <!-- Red -->

<!-- Using CSS variables (recommended) -->
<i class="fas fa-chart-line" style="color: var(--first-color);"></i>
```

**Colors Used in App:**
- Primary: `#ff5532` (orange)
- Success: `#166534` (green)
- Danger: `#991b1b` (red)
- Muted: `#b9b9b9` (gray)

---

## Accessibility

All icons include text labels for screen readers:

```html
<!-- Proper accessibility -->
<label>
    <i class="fas fa-envelope"></i> Email Address
</label>

<!-- Link with accessible label -->
<a href="/courses" class="nav-link">
    <i class="fas fa-book"></i>
    <span>Courses</span>
</a>
```

**Important:** Always include text labels alongside icons for accessibility.

---

## Finding Icons

### Official Font Awesome Icon Database
- **URL:** https://fontawesome.com/icons
- **Search:** Yes, by keyword
- **Filter:** By category, style, or popularity

### Categories Available
- Business
- UI Controls
- Web Application
- Accessibility
- Medical
- Hands
- Arrows
- Video & Music
- Commerce
- Editing
- Weather
- Mathematics
- Transportation
- Transportation
- Places
- Foods
- Nature
- Objects
- Users
- Spinners

---

## Quick Icon Replacement Commands

**For VS Code:**
1. Use Find & Replace (Ctrl+H)
2. Find: `fa-chart-line`
3. Replace with: `fa-home`
4. Click "Replace All" for all instances

---

## Tips & Best Practices

1. **Consistency:** Use the same icon for the same action throughout the app
2. **Clarity:** Choose icons that clearly represent the action
3. **Testing:** Always test icons on both light and dark themes
4. **Mobile:** Ensure icons are large enough on small screens
5. **Accessibility:** Always pair icons with text labels
6. **Performance:** Font Awesome CDN is cached by browsers

---

## Common Icon Names (Quick Lookup)

```
🏠 home, house, dashboard
👥 users, people, group, team
📚 book, education, library, read
💰 money, dollar, invoice, fee, payment
💼 briefcase, work, business, portfolio
⚙️ cog, setting, gear, config
🔔 bell, notification, alert, sound
📊 chart, graph, analytics, statistics
📅 calendar, date, day, schedule
⏰ clock, time, watch, history
🔒 lock, security, password, private
🔓 unlock, open, unlocked
📧 envelope, email, letter, mail
🔍 search, find, magnifying-glass
✅ check, success, done, complete
❌ times, close, delete, remove
⚠️ warning, alert, danger, caution
ℹ️ info, information, details
🚪 door, exit, logout, signout
🎓 graduation-cap, education, graduate
🌙 moon, night, dark, theme
☀️ sun, light, day, bright
```

---

## External Resources

- **Font Awesome Official:** https://fontawesome.com/
- **Icon Gallery:** https://fontawesome.com/icons
- **Documentation:** https://fontawesome.com/docs
- **Getting Started:** https://fontawesome.com/docs/web/setup/get-started

---

**Happy Icon Hunting! 🎨**
