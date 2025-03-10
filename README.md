# Champions League Simulation - Laravel Project

## 📌 Overview
This Laravel project simulates the UEFA Champions League tournament. Users can manage teams, create groups, simulate matches, and progress through the knockout stages to determine the champion.

## 🛠️ Installation

### 1. Clone the Repository
```bash
 git clone https://github.com/digitalemekci/FootballSimulation.git
 cd FootballSimulation
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Set Up Environment File
```bash
cp .env.example .env
```
Update the `.env` file with your database credentials.

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations and Seed Database
```bash
php artisan migrate --seed
```

### 6. Start the Development Server
```bash
php artisan serve
```

## 🔗 API Endpoints & Commands
Users can perform the following actions via UI or API:

### ✅ Team Management
- Import teams:
  ```bash
  php artisan import:champions-league-teams
  ```

### ✅ Group Stage
- Generate groups:
  ```bash
  php artisan generate:groups
  ```
- Assign teams to groups:
  ```bash
  php artisan assign:teams-to-groups
  ```
- Generate match fixtures:
  ```bash
  php artisan generate:fixtures
  ```
- Simulate group stage matches:
  ```bash
  php artisan simulate:matches
  ```
- Calculate group standings:
  ```bash
  php artisan calculate:group-standings
  ```

### ✅ Knockout Stage
- Start knockout rounds:
  ```bash
  php artisan generate:knockout-stage
  ```
- Simulate knockout matches:
  ```bash
  php artisan simulate:knockout-matches
  ```
- Generate and simulate Round of 16:
  ```bash
  php artisan generate:knockout-stage
  php artisan simulate:knockout-matches
  ```
- Update championship probability:
  ```bash
  php artisan update-win-probability
  ```

### ✅ Quarter-Finals
- Start quarter-finals:
  ```bash
  php artisan start:quarter-finals
  ```
- Simulate quarter-final matches:
  ```bash
  php artisan simulate:knockout-matches
  ```
- Update championship probability:
  ```bash
  php artisan update-win-probability
  ```

### ✅ Semi-Finals
- Start semi-finals:
  ```bash
  php artisan start:semi-finals
  ```
- Simulate semi-final matches:
  ```bash
  php artisan simulate:knockout-matches
  ```
- Update championship probability:
  ```bash
  php artisan update-win-probability
  ```

### ✅ Final
- Start the final match:
  ```bash
  php artisan start:final
  ```
- Simulate the final match:
  ```bash
  php artisan simulate:knockout-matches
  ```

## 🚀 Deployment
### Build Production Files
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Run Queue Worker
```bash
php artisan queue:work
```
### Run Frontend
```bash
npm run dev
```

## 👨‍💻 Contributing
Feel free to submit issues or pull requests for improvements.

## 📞 Contact
For inquiries, reach out at [digitalemekci@gmail.com](mailto:digitalemekci@gmail.com).

