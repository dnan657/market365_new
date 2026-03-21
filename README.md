# UK Classifieds — Полноценная платформа доски объявлений для Великобритании

UK Classifieds — это современная, высокопроизводительная платформа для размещения объявлений, созданная специально для британского рынка. Проект вдохновлен такими сервисами, как 999.md, Avito и OLX.

## 🚀 Основные функции

-   **Продвинутая Авторизация**: Поддержка Google OAuth и вход через Email (Magic Links) с использованием NextAuth.js.
-   **Система объявлений**: Создание, редактирование и просмотр объявлений с поддержкой категорий, изображений и карт.
-   **Прием платежей**: Интеграция со **Stripe** для платного продвижения объявлений (Featured Ads).
-   **Чаты и Сообщения**: Система личных сообщений в реальном времени между покупателями и продавцами.
-   **Полнотекстовый поиск**: Улучшенный поиск с фильтрацией по регионам UK, категориям, цене и сортировкой.
-   **Уведомления**: Центр уведомлений внутри приложения (входящие сообщения, статус платежей).
-   **Панель администратора (RBAC)**: Управление платформой с разделением ролей (SUPERADMIN, MODERATOR).
-   **Умная модерация (AI)**: Автоматическая проверка контента на наличие спама и скама.

## 🛠 Технологический стек

-   **Frontend/Backend**: Next.js 15 (App Router)
-   **Аутентификация**: NextAuth.js (Auth.js)
-   **Платежи**: Stripe API
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

# Stripe (dashboard.stripe.com)
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."

# Resend (resend.com)
RESEND_API_KEY="re_..."

# Admin
ADMIN_PASSWORD="uk-admin-2026"
```

## 🐳 Развертывание через Docker

Самый быстрый способ запустить платформу — использовать Docker Compose.

1.  **Убедитесь, что у вас установлен Docker**.
2.  **Запустите проект**:
    ```bash
    docker compose up --build
    ```
3.  **Инициализируйте базу данных** (в новом окне):
    ```bash
    docker compose exec app npx prisma db push
    docker compose exec app npx ts-node prisma/seed.ts
    ```
4.  **Готово!** Приложение доступно по адресу `http://localhost:3000`.

---

## 🔒 Роли в системе
-   **USER**: Обычный пользователь. Может подавать объявления, общаться в чате.
-   **MODERATOR**: Доступ к админ-панели для одобрения/отклонения объявлений и тикетов.
-   **SUPERADMIN**: Полный доступ, включая управление ролями пользователей и просмотр финансовой статистики.
