# UK Classifieds — Полноценная платформа доски объявлений для Великобритании

UK Classifieds — это современная, высокопроизводительная платформа для размещения объявлений, созданная специально для британского рынка. Проект вдохновлен такими сервисами, как 999.md, Avito и OLX.

## 🚀 Основные функции

-   **Продвинутая Авторизация**: Поддержка Google OAuth и вход через Email (Magic Links) с защитой от ботов через **Google reCAPTCHA v2**.
-   **Система объявлений**: Создание, редактирование и просмотр объявлений с поддержкой категорий, изображений и интерактивных карт.
-   **Интеграция с Рекламой**: Встроенная поддержка **Google AdSense** для монетизации трафика (отображение в поиске и карточках товаров).
-   **Прием платежей**: Интеграция со **Stripe** для платного продвижения объявлений (Featured Ads).
-   **Чаты и Сообщения**: Система личных сообщений в реальном времени между покупателями и продавцами.
-   **Полнотекстовый поиск**: Улучшенный поиск с фильтрацией по регионам UK, категориям, цене и сортировкой.
-   **Уведомления**: Центр уведомлений внутри приложения (сообщения, статусы платежей).
-   **Панель администратора (RBAC)**: Управление платформой с разделением ролей (SUPERADMIN, MODERATOR).
-   **Умная модерация (AI)**: Автоматическая проверка контента на наличие спама и скама.

## 🛠 Технологический стек

-   **Frontend/Backend**: Next.js 15 (App Router)
-   **Аутентификация**: NextAuth.js + react-google-recaptcha
-   **Платежи**: Stripe API
-   **Реклама**: Google AdSense
-   **Email**: Resend API
-   **База данных/ORM**: Prisma с PostgreSQL
-   **Контейнеризация**: Docker & Docker Compose

## ⚙️ Настройка ключей (Environment Variables)

Для работы всех функций необходимо создать файл `.env` на основе следующих ключей:

```env
# База данных
DATABASE_URL="postgresql://user:password@localhost:5432/ukclassifieds?schema=public"

# NextAuth
NEXTAUTH_URL="http://localhost:3000"
NEXTAUTH_SECRET="произвольная-строка-для-защиты-сессий"

# Google OAuth (console.cloud.google.com)
GOOGLE_CLIENT_ID="ваш-client-id"
GOOGLE_CLIENT_SECRET="ваш-client-secret"

# Google reCAPTCHA (google.com/recaptcha/admin)
NEXT_PUBLIC_RECAPTCHA_SITE_KEY="ваш-site-key"
RECAPTCHA_SECRET_KEY="ваш-secret-key"

# Google AdSense
NEXT_PUBLIC_GOOGLE_ADSENSE_ID="pub-xxxxxxxxxxxxxxxx"

# Stripe (dashboard.stripe.com)
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."

# Resend (resend.com)
RESEND_API_KEY="re_..."

# Admin
ADMIN_PASSWORD="uk-admin-2026"
```

## 🐳 Развертывание через Docker

1.  **Убедитесь, что у вас установлен Docker**.
2.  **Запустите проект**:
    ```bash
    docker compose up --build
    ```
3.  **Инициализируйте базу данных**:
    ```bash
    docker compose exec app npx prisma db push
    docker compose exec app npx ts-node prisma/seed.ts
    ```
4.  **Готово!** Приложение доступно по адресу `http://localhost:3000`.
