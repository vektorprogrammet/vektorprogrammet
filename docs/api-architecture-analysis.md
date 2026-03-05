# Vektorprogrammet — Architectural Analysis

## Table of Contents

1. [High-Level System Overview](#1-high-level-system-overview)
2. [Module Map](#2-module-map)
3. [Dual API Architecture](#3-dual-api-architecture)
4. [API Platform Request → Response Lifecycle](#4-api-platform-request--response-lifecycle)
5. [Entity Relationship Model](#5-entity-relationship-model)
6. [Security & Authentication Flow](#6-security--authentication-flow)
7. [Event-Driven Side Effects](#7-event-driven-side-effects)
8. [Data Flow: Application Submission (End-to-End)](#8-data-flow-application-submission-end-to-end)
9. [Service Layer](#9-service-layer)
10. [Frontend Integration](#10-frontend-integration)

---

## 1. High-Level System Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          CLIENTS                                            │
│                                                                             │
│   ┌──────────────┐   ┌──────────────────┐   ┌───────────────────────┐      │
│   │  v2 React    │   │  Twig SSR Pages  │   │  Admin Panel (Twig)   │      │
│   │  SPA (Vite)  │   │  (public site)   │   │  /kontrollpanel/*     │      │
│   └──────┬───────┘   └────────┬─────────┘   └───────────┬───────────┘      │
│          │ JWT                │ session                  │ session          │
└──────────┼────────────────────┼─────────────────────────┼──────────────────┘
           │                    │                          │
           ▼                    ▼                          ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                         SYMFONY 6.4 KERNEL                                   │
│                                                                              │
│  ┌─────────────────────┐  ┌──────────────────┐  ┌────────────────────────┐  │
│  │  API Platform 3.4   │  │  ~61 Controllers │  │  FOS REST Controllers  │  │
│  │  /api/*  (JWT)      │  │  Twig routes     │  │  /api/party/*          │  │
│  │  stateless          │  │  session-based   │  │  session-based         │  │
│  └─────────┬───────────┘  └────────┬─────────┘  └──────────┬─────────────┘  │
│            │                       │                        │                │
│            ▼                       ▼                        ▼                │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     SERVICE LAYER                                    │     │
│  │  ApplicationAdmission · PasswordManager · AdmissionNotifier         │     │
│  │  InterviewManager · SurveyManager · RoleManager · FileUploader      │     │
│  │  AccessControlService · EmailSender · SlackMessenger                 │     │
│  └──────────────────────────────┬──────────────────────────────────────┘     │
│                                 │                                            │
│  ┌──────────────────────────────▼──────────────────────────────────────┐     │
│  │                   DOCTRINE ORM (SQLite test / MySQL prod)           │     │
│  │                   40+ Entities · Repository pattern                  │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                 EVENT DISPATCHER                                      │     │
│  │  14 EventSubscribers: mail, history, GSuite, Slack, receipts, etc.  │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Module Map

```
src/App/
├── ApiResource/          # 9 DTO classes (API Platform write inputs + ProfileResource)
│   ├── ApplicationInput          POST /applications
│   ├── ExistingUserApplicationInput  POST /applications/existing
│   ├── ContactMessageInput       POST /contact_messages
│   ├── AdmissionSubscriberInput  POST /admission_subscribers
│   ├── TeamApplicationInput      POST /team_applications
│   ├── PasswordResetRequest      POST /password_resets
│   ├── PasswordResetExecute      POST /password_resets/{code}
│   ├── ProfileResource           GET|PUT /me
│   └── Statistics                GET /statistics
│
├── State/                # Processors (write) + Providers (read)
│   ├── ApplicationProcessor
│   ├── ExistingUserApplicationProcessor
│   ├── ContactMessageProcessor
│   ├── AdmissionSubscriberProcessor
│   ├── TeamApplicationProcessor
│   ├── PasswordResetRequestProcessor
│   ├── PasswordResetExecuteProcessor
│   ├── ProfileProcessor
│   ├── ArticleProcessor
│   ├── ProfileProvider
│   ├── StatisticsProvider
│   └── PublicUserProfileProvider
│
├── Entity/               # 40+ Doctrine entities (12 with API Platform #[ApiResource])
│   ├── User, Department, Team, Role
│   ├── Application, AdmissionPeriod, AdmissionSubscriber
│   ├── Interview, InterviewSchema, InterviewQuestion, InterviewAnswer
│   ├── Survey, SurveyQuestion, SurveyAnswer, SurveyTaken
│   ├── Article, Sponsor, StaticContent, ChangeLogItem
│   ├── School, SchoolCapacity, AssistantHistory
│   ├── Receipt, Signature, CertificateRequest
│   ├── TeamMembership, ExecutiveBoardMembership, TeamApplication
│   ├── FieldOfStudy, Semester, Position
│   └── ...more
│
├── Controller/           # ~61 Twig controllers + FOS REST controllers
├── Service/              # ~32 business logic services
├── EventSubscriber/      # 14 event subscribers
├── Security/             # UserChecker, voters
├── Mailer/               # Mail abstraction
├── Sms/                  # SMS sending
├── Twig/                 # Twig extensions
└── Validator/            # Custom validators
```

---

## 3. Dual API Architecture

This application runs **two API systems in parallel**, partitioned by URL and firewall:

```
                         ┌───────────────────────────────────┐
                         │         Incoming Request           │
                         │         /api/...                   │
                         └───────────────┬───────────────────┘
                                         │
                              ┌──────────▼──────────┐
                              │  Symfony Firewall    │
                              │  Chain Resolution    │
                              └──────────┬──────────┘
                                         │
                    ┌────────────────────┼────────────────────┐
                    │                    │                     │
           ┌────────▼──────┐   ┌────────▼──────┐   ┌────────▼────────┐
           │ ^/api/login   │   │ ^/api/party   │   │ ^/api (other)   │
           │ api_login fw  │   │ api_party fw  │   │ api firewall    │
           │               │   │               │   │                 │
           │ JSON Login    │   │ Session auth  │   │ JWT (stateless) │
           │ → JWT token   │   │ Lazy loading  │   │ lexik_jwt       │
           └───────────────┘   │ UserChecker   │   └────────┬────────┘
                               └───────┬───────┘            │
                                       │                     │
                              ┌────────▼──────┐   ┌────────▼────────┐
                              │  FOS REST     │   │  API Platform   │
                              │  Controllers  │   │  Resource Layer  │
                              │               │   │                 │
                              │  Manual JSON  │   │  Auto routing   │
                              │  responses    │   │  Serialization  │
                              │  format_lstnr │   │  Validation     │
                              └───────────────┘   │  OpenAPI docs   │
                                                  └─────────────────┘

Key coexistence rule: FOS REST format_listener is scoped to ^/api/party
with a stop:true rule at ^/api/ to prevent it intercepting API Platform.
```

### API Platform Endpoints (JWT / Public)

| Method | Route | Auth | Handler Type |
|--------|-------|------|-------------|
| GET | `/api/departments` | Public | Entity auto-serialization |
| GET | `/api/departments/{id}` | Public | Entity auto-serialization |
| GET | `/api/teams` | Public | Entity auto-serialization |
| GET | `/api/articles` | Public | Entity auto-serialization |
| GET | `/api/sponsors` | Public | Entity auto-serialization |
| GET | `/api/admission_periods` | Public | Entity auto-serialization |
| GET | `/api/info_meetings` | Public | Entity auto-serialization |
| GET | `/api/field_of_studies` | Public | Entity auto-serialization |
| GET | `/api/team_memberships` | Public | Entity auto-serialization |
| GET | `/api/static_contents` | Public | Entity auto-serialization |
| GET | `/api/change_log_items` | Public | Entity auto-serialization |
| GET | `/api/users/{id}` | Public | Custom Provider (PublicUserProfileProvider) |
| GET | `/api/statistics` | Public | Custom Provider (StatisticsProvider) |
| GET | `/api/me` | JWT | Custom Provider (ProfileProvider) |
| PUT | `/api/me` | JWT | Custom Provider + Processor |
| POST | `/api/applications` | Public | Custom DTO + Processor |
| POST | `/api/applications/existing` | JWT | Custom DTO + Processor |
| POST | `/api/contact_messages` | Public | Custom DTO + Processor |
| POST | `/api/admission_subscribers` | Public | Custom DTO + Processor |
| POST | `/api/password_resets` | Public | Custom DTO + Processor |
| POST | `/api/password_resets/{code}` | Public | Custom DTO + Processor |
| POST | `/api/team_applications` | Public | Custom DTO + Processor |
| POST/PUT/DEL | `/api/articles` | ADMIN | Entity + ArticleProcessor |

### FOS REST Endpoints (Session)

| Pattern | Auth | Purpose |
|---------|------|---------|
| `/api/party/*` | ROLE_TEAM_MEMBER (session) | Legacy admin/team API |

---

## 4. API Platform Request → Response Lifecycle

This is the complete journey of an HTTP request through the API Platform stack:

```
  HTTP Request
  POST /api/applications
  Content-Type: application/json
  Body: {"firstName":"Ola","email":"ola@ntnu.no",...}
       │
       ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: SYMFONY KERNEL BOOT                                             │
│                                                                          │
│  HttpKernel::handle()                                                    │
│    └─ kernel.request event                                               │
│         ├─ RouterListener: matches route to API Platform operation        │
│         ├─ FirewallListener: selects "api" firewall (pattern: ^/api)     │
│         └─ AccessListener: checks access_control rules                   │
│              → /api/applications = PUBLIC_ACCESS ✓                        │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: API PLATFORM ROUTING & METADATA RESOLUTION                      │
│                                                                          │
│  API Platform's MainController receives the request                      │
│    └─ ResourceMetadataFactory resolves:                                   │
│         ├─ Resource class: ApplicationInput                               │
│         ├─ Operation: Post                                                │
│         ├─ uriTemplate: /applications                                     │
│         ├─ processor: ApplicationProcessor::class                         │
│         ├─ output: false (no response body)                               │
│         └─ status: 201                                                    │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 3: DESERIALIZATION                                                 │
│                                                                          │
│  DeserializeListener (kernel.request, priority 4)                        │
│    └─ Symfony Serializer:                                                 │
│         ├─ Decodes JSON body                                              │
│         ├─ Denormalizes into ApplicationInput object                      │
│         ├─ Maps JSON keys → public properties:                            │
│         │    firstName, lastName, email, phone,                           │
│         │    departmentId, fieldOfStudyId, yearOfStudy,                   │
│         │    gender, monday..friday, substitute, etc.                     │
│         └─ Sets defaults for unset properties (e.g., language="Norsk")   │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 4: SECURITY CHECK (operation-level)                                │
│                                                                          │
│  SecurityPostDenormalizeListener checks:                                  │
│    └─ Operation has no "security" attribute                               │
│    └─ → PASS (public endpoint, no extra check needed)                     │
│                                                                          │
│  For authenticated endpoints (e.g., POST /applications/existing):        │
│    └─ security: "is_granted('ROLE_USER')"                                │
│    └─ ExpressionLanguage evaluates against SecurityContext                │
│    └─ JWT token already decoded by firewall → User in TokenStorage       │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 5: VALIDATION                                                      │
│                                                                          │
│  ValidateListener (kernel.view, priority 64)                             │
│    └─ Symfony Validator evaluates #[Assert\...] on ApplicationInput:     │
│         ├─ #[Assert\NotBlank] on firstName, lastName, email, phone       │
│         ├─ #[Assert\Email] on email                                       │
│         ├─ #[Assert\NotNull] on fieldOfStudyId, gender, departmentId     │
│         ├─ #[Assert\Choice([0,1])] on gender                              │
│         └─ If violations → 422 Unprocessable Entity                       │
│              {                                                            │
│                "violations": [{                                           │
│                  "propertyPath": "email",                                 │
│                  "message": "This value is not a valid email address."    │
│                }]                                                         │
│              }                                                            │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │  (validation passed)
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 6: STATE PROCESSOR (business logic)                                │
│                                                                          │
│  WriteListener (kernel.view, priority 32)                                │
│    └─ ApplicationProcessor::process($data, $operation, ...)              │
│                                                                          │
│         ┌──────────────────────────────────────────┐                     │
│         │  1. Validate domain constraints:          │                     │
│         │     - Department exists?                   │                     │
│         │     - Active admission period?             │                     │
│         │     - FieldOfStudy exists?                 │                     │
│         │     (throw 422 if not)                     │                     │
│         ├──────────────────────────────────────────┤                     │
│         │  2. Find or create User:                   │                     │
│         │     - Query by email                       │                     │
│         │     - If null → new User()                 │                     │
│         │       set name, phone, gender, FoS         │                     │
│         │       add ROLE_ASSISTANT                    │                     │
│         ├──────────────────────────────────────────┤                     │
│         │  3. Create Application entity:             │                     │
│         │     - Link user + admissionPeriod          │                     │
│         │     - Set schedule, preferences            │                     │
│         ├──────────────────────────────────────────┤                     │
│         │  4. Persist & flush:                       │                     │
│         │     $em->persist($application)             │                     │
│         │     $em->flush()                           │                     │
│         ├──────────────────────────────────────────┤                     │
│         │  5. Dispatch event:                        │                     │
│         │     ApplicationCreatedEvent                │                     │
│         │       ├─→ sendConfirmationMail()           │                     │
│         │       └─→ createAdmissionSubscriber()      │                     │
│         └──────────────────────────────────────────┘                     │
│                                                                          │
│  Processor returns void (output: false)                                  │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 7: SERIALIZATION & RESPONSE                                        │
│                                                                          │
│  SerializeListener (kernel.view)                                         │
│    └─ output: false → skip serialization                                 │
│    └─ Return empty body with status 201                                  │
│                                                                          │
│  For GET operations (e.g., GET /api/departments):                        │
│    └─ Serializer normalizes entity using:                                │
│         ├─ normalizationContext groups (e.g., ['department:read'])        │
│         ├─ Only properties with matching #[Groups] are included          │
│         ├─ Related entities follow their own groups                       │
│         └─ JSON-LD or JSON format based on Accept header                 │
│                                                                          │
│  RespondListener sets final response headers                             │
└───────────────────────────────┬──────────────────────────────────────────┘
                                │
                                ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ PHASE 8: KERNEL RESPONSE & TERMINATE                                     │
│                                                                          │
│  kernel.response event:                                                  │
│    └─ CORS headers (NelmioCorsBundle)                                    │
│    └─ Cache headers                                                      │
│                                                                          │
│  kernel.terminate event (after response sent):                           │
│    └─ Any deferred logging                                               │
│    └─ Profiler data collection (dev)                                     │
│                                                                          │
│  HTTP Response → Client                                                  │
│  201 Created (empty body)                                                │
└──────────────────────────────────────────────────────────────────────────┘
```

### Read Operation Lifecycle (GET /api/me)

```
  GET /api/me
  Authorization: Bearer <jwt>
       │
       ▼
  ┌─ Firewall (api) ─────────────────────────────┐
  │  JWT Authenticator decodes token              │
  │  Chain provider finds User by username/email  │
  │  User placed in SecurityContext               │
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ Access Control ─────▼────────────────────────┐
  │  ^/api/me → IS_AUTHENTICATED_FULLY ✓          │
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ API Platform ───────▼────────────────────────┐
  │  Resource: ProfileResource                     │
  │  Operation: Get                                │
  │  security: is_granted('ROLE_USER') ✓           │
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ Provider ───────────▼────────────────────────┐
  │  ProfileProvider::provide()                    │
  │    $user = $this->security->getUser()          │
  │    return ProfileResource::fromUser($user)     │
  │      → maps id, name, email, phone, gender     │
  │      → maps fieldOfStudy as nested array       │
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ Serialization ──────▼────────────────────────┐
  │  Serializer normalizes ProfileResource         │
  │  All public properties included (no groups)    │
  │  Output: JSON                                  │
  └──────────────────────┬────────────────────────┘
                         │
                         ▼
  200 OK
  {"id":1,"firstName":"Ola","lastName":"Nordmann",
   "email":"ola@ntnu.no","phone":"12345678",
   "gender":1,"fieldOfStudy":{"id":3,"name":"Fysikk",...}}
```

### Entity Read Lifecycle (GET /api/departments)

```
  GET /api/departments
       │
       ▼
  ┌─ No auth needed (PUBLIC_ACCESS) ──────────────┐
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ Default Provider ───▼────────────────────────┐
  │  Doctrine CollectionProvider                   │
  │    SELECT * FROM department                    │
  │    Pagination: ?page=1 (30 items/page default) │
  └──────────────────────┬────────────────────────┘
                         │
  ┌─ Serialization ──────▼────────────────────────┐
  │  Groups: ['department:read']                   │
  │  Include: id, name, shortName, email,          │
  │           address, city, lat, lng              │
  │  Exclude: internal relations (no group)        │
  └──────────────────────┬────────────────────────┘
                         │
                         ▼
  200 OK (JSON-LD collection with hydra pagination)
```

---

## 5. Entity Relationship Model

```
                                ┌─────────────┐
                        ┌──────│ Department  │──────┐
                        │      └──────┬──────┘      │
                        │             │              │
                 has many│      has many│       has many│
                        ▼             ▼              ▼
               ┌──────────┐  ┌──────────────┐  ┌──────┐
               │ Team     │  │ AdmissionPrd │  │School│
               └────┬─────┘  └──────┬───────┘  └──┬───┘
                    │               │              │
             has many│        has many│       has many│
                    ▼               ▼              ▼
          ┌──────────────┐  ┌──────────────┐  ┌────────────┐
          │TeamMembership│  │ Application  │  │SchoolCpcty │
          └──────┬───────┘  └──────┬───────┘  └────────────┘
                 │                 │
          belongs│          belongs│
            to   │            to   │
                 ▼                 ▼
          ┌──────────────────────────┐
          │          User            │
          │                          │
          │  email, name, phone      │
          │  roles[], password       │
          │  fieldOfStudy ──────────────────▶ FieldOfStudy
          │  department (derived)    │
          │  assistantHistories[] ──────────▶ AssistantHistory
          │  teamMemberships[] ─────────────▶ TeamMembership
          │  receipts[] ───────────────────▶ Receipt
          │  signatures[] ─────────────────▶ Signature
          └──────────┬──────────────┘
                     │
              has many│
                     ▼
          ┌──────────────────┐         ┌──────────────────┐
          │   Application    │────────▶│  AdmissionPeriod │
          │                  │         └──────────────────┘
          │  yearOfStudy     │
          │  mon-fri avail   │
          │  language, prefs │
          │  interview ──────┼────────▶ Interview
          └──────────────────┘              │
                                            │ has many
                                            ▼
                                     ┌──────────────┐
                                     │InterviewAnswer│
                                     └──────┬───────┘
                                            │ answers
                                            ▼
                                     ┌──────────────────┐
                                     │InterviewQuestion  │
                                     │(from InterviewSchema)│
                                     └──────────────────┘

  ┌──────────────┐      ┌────────────────┐      ┌──────────────┐
  │   Survey     │─────▶│ SurveyQuestion │─────▶│SurveyQAlt   │
  └──────┬───────┘      └────────────────┘      └──────────────┘
         │
         │ taken by
         ▼
  ┌──────────────┐
  │ SurveyTaken  │──▶ SurveyAnswer[]
  └──────────────┘

  ┌──────────────┐    ┌──────────────────┐    ┌──────────────┐
  │  Article     │    │  Sponsor         │    │ StaticContent│
  │  (blog/news) │    │  (logo, url)     │    │ (CMS pages)  │
  └──────────────┘    └──────────────────┘    └──────────────┘
```

### Domain Aggregates

| Aggregate Root | Children | Purpose |
|---------------|----------|---------|
| **User** | TeamMembership, AssistantHistory, Receipt, Signature | Person in the system |
| **Department** | Team, AdmissionPeriod, School, FieldOfStudy | University chapter |
| **Application** | Interview → InterviewAnswer | Tutor application + evaluation |
| **Survey** | SurveyQuestion → Alternative, SurveyTaken → Answer | Feedback collection |
| **Team** | TeamMembership, TeamApplication | Organizational unit |

---

## 6. Security & Authentication Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION METHODS                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. JWT (API Platform)           2. Session (Twig + FOS REST)   │
│                                                                  │
│  POST /api/login                 POST /login_check               │
│  {"username":"x","password":"y"} form_login authenticator        │
│       │                               │                          │
│       ▼                               ▼                          │
│  ┌──────────────┐              ┌──────────────┐                 │
│  │ json_login   │              │ form_login   │                 │
│  │ authenticator│              │ authenticator│                 │
│  └──────┬───────┘              └──────┬───────┘                 │
│         │                             │                          │
│         └────────────┬────────────────┘                          │
│                      ▼                                           │
│           ┌──────────────────────┐                               │
│           │  Chain User Provider  │                               │
│           │  1. by user_name     │                               │
│           │  2. by email         │                               │
│           │  3. by companyEmail  │                               │
│           └──────────┬───────────┘                               │
│                      │                                           │
│                      ▼                                           │
│           ┌──────────────────────┐                               │
│           │  UserChecker         │                               │
│           │  Pre:  isEnabled?    │                               │
│           │  Post: locked?       │                               │
│           │        expired?      │                               │
│           │        creds expired?│                               │
│           └──────────┬───────────┘                               │
│                      │                                           │
│                      ▼                                           │
│           ┌──────────────────────┐                               │
│           │  bcrypt verify       │                               │
│           │  (cost: 12)          │                               │
│           └──────────┬───────────┘                               │
│                      │                                           │
│         ┌────────────┴────────────┐                              │
│         │ JWT path                │ Session path                 │
│         ▼                         ▼                              │
│  ┌──────────────┐         ┌──────────────┐                      │
│  │ Lexik JWT    │         │ Session      │                      │
│  │ success_hndlr│         │ + remember_me│                      │
│  │ → token resp │         │ (1yr cookie) │                      │
│  └──────────────┘         └──────────────┘                      │
│                                                                  │
│  Token TTL: 3600s                                                │
│  Keys: config/jwt/private.pem + public.pem                      │
└─────────────────────────────────────────────────────────────────┘

ROLE HIERARCHY (linear):
  ROLE_USER → ROLE_TEAM_MEMBER → ROLE_TEAM_LEADER → ROLE_ADMIN
      │              │                  │                │
      │              │                  │                └─ Full admin panel
      │              │                  └─ Dept management, interviews
      │              └─ /kontrollpanel, /api/party, file mgmt
      └─ Profile edit, receipts, /api/me
```

### Firewall Dispatch Order

```
Request: /api/applications
  → matches ^/api (api firewall, JWT, stateless)
  → access_control: PUBLIC_ACCESS ✓

Request: /api/me
  → matches ^/api (api firewall, JWT, stateless)
  → JWT decoded → User in context
  → access_control: IS_AUTHENTICATED_FULLY ✓
  → operation security: is_granted('ROLE_USER') ✓

Request: /api/party/events
  → matches ^/api/party (api_party firewall, session, lazy)
  → session cookie → User in context
  → access_control: ROLE_TEAM_MEMBER ✓

Request: /kontrollpanel/opptak
  → matches secured_area firewall (session, form_login)
  → access_control: ROLE_TEAM_MEMBER ✓
```

---

## 7. Event-Driven Side Effects

```
┌─────────────────────────────────────────────────────────────────┐
│                    EVENT DISPATCH FLOW                            │
│                                                                  │
│  State Processor                                                 │
│       │                                                          │
│       │  dispatch(Event)                                         │
│       ▼                                                          │
│  EventDispatcher                                                 │
│       │                                                          │
│       │  resolves subscribers by event name + priority            │
│       ▼                                                          │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  ApplicationCreatedEvent                                 │    │
│  │    priority 0:  sendConfirmationMail()                   │    │
│  │      → Email to applicant (different template for        │    │
│  │        new vs. returning assistants)                      │    │
│  │      → If new user: generates activation code             │    │
│  │    priority -2: createAdmissionSubscriber()              │    │
│  │      → Auto-subscribe to department admission notices     │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  TeamApplicationCreatedEvent                             │    │
│  │    priority 0:  sendConfirmationMail() → applicant email │    │
│  │    priority 0:  sendApplicationToTeamMail() → team email │    │
│  │    priority -1: addFlashMessage() → "Søknaden er mottatt"│    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                  │
│  Other domain events (Doctrine lifecycle / kernel events):       │
│    • AccessControlSubscriber — permission cache                  │
│    • AssistantHistorySubscriber — tracks assistant semesters     │
│    • DbSubscriber — Doctrine lifecycle hooks                     │
│    • GSuiteSubscriber — Google Workspace sync                    │
│    • InterviewSubscriber — schedule notifications                │
│    • IntroductionEmailSubscriber — welcome emails                │
│    • ReceiptSubscriber — expense receipt processing               │
│    • SupportTicketSubscriber — ticket notifications              │
│    • TeamInterestSubscriber — notify teams of interested users   │
│    • TeamMembershipSubscriber — membership lifecycle             │
│    • UserSubscriber — user lifecycle events                      │
│    • ExceptionSubscriber — error logging                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## 8. Data Flow: Application Submission (End-to-End)

Complete trace from browser click to database write and email sent:

```
  React SPA (v2)
  User fills application form
       │
       │  POST /api/applications
       │  Content-Type: application/json
       │  {"firstName":"Ola","lastName":"Nordmann",
       │   "email":"ola@ntnu.no","departmentId":1,
       │   "fieldOfStudyId":3,"yearOfStudy":"3",
       │   "gender":1,"monday":true,...}
       │
       ▼
  ┌─ SYMFONY KERNEL ──────────────────────────────────────────────────┐
  │                                                                    │
  │  1. FIREWALL: api (^/api, stateless, JWT)                         │
  │     → No JWT needed (PUBLIC_ACCESS)                                │
  │                                                                    │
  │  2. API PLATFORM ROUTING                                           │
  │     → Matches POST /applications → ApplicationInput resource       │
  │                                                                    │
  │  3. DESERIALIZATION                                                │
  │     → JSON → ApplicationInput DTO                                  │
  │     → Properties populated from request body                       │
  │                                                                    │
  │  4. VALIDATION                                                     │
  │     → #[Assert\NotBlank] on firstName ✓                            │
  │     → #[Assert\Email] on email ✓                                   │
  │     → #[Assert\NotNull] on departmentId ✓                          │
  │     → #[Assert\Choice([0,1])] on gender ✓                          │
  │     → All constraints pass                                         │
  │                                                                    │
  │  5. ApplicationProcessor::process()                                │
  │     │                                                              │
  │     ├─ DepartmentRepo::find(1) → Department{NTNU}                 │
  │     ├─ AdmissionPeriodRepo::findActive(NTNU) → AdmPeriod{V2026}   │
  │     ├─ FieldOfStudyRepo::find(3) → FieldOfStudy{Fysikk}          │
  │     │                                                              │
  │     ├─ UserRepo::findOneBy({email:"ola@ntnu.no"})                 │
  │     │   → null (new user)                                          │
  │     │                                                              │
  │     ├─ CREATE User                                                 │
  │     │   email=ola@ntnu.no, firstName=Ola, lastName=Nordmann       │
  │     │   phone, gender=1, fieldOfStudy=Fysikk                      │
  │     │   role += ROLE_ASSISTANT                                     │
  │     │                                                              │
  │     ├─ CREATE Application                                          │
  │     │   user=User, admissionPeriod=V2026                           │
  │     │   yearOfStudy=3, monday=true, ... etc                        │
  │     │                                                              │
  │     ├─ EntityManager::persist(application) + flush()               │
  │     │   → INSERT INTO user ...                                     │
  │     │   → INSERT INTO application ...                              │
  │     │                                                              │
  │     └─ EventDispatcher::dispatch(ApplicationCreatedEvent)          │
  │         │                                                          │
  │         ├─ [pri 0] ApplicationSubscriber::sendConfirmationMail()   │
  │         │   ├─ User has no password → generate new_user_code       │
  │         │   ├─ Template: admission/admission_email.html.twig       │
  │         │   ├─ Subject: "Søknad - Vektorassistent"                 │
  │         │   ├─ To: ola@ntnu.no                                     │
  │         │   ├─ ReplyTo: department email                           │
  │         │   └─ Mailer::send()                                      │
  │         │                                                          │
  │         └─ [pri -2] ApplicationSubscriber::createAdmissionSubscr() │
  │             └─ AdmissionNotifier::createSubscription(NTNU, email)  │
  │                                                                    │
  │  6. RESPONSE                                                       │
  │     → output: false → empty body                                   │
  │     → status: 201 Created                                          │
  │                                                                    │
  └────────────────────────────────────────────────────────────────────┘
       │
       ▼
  HTTP/1.1 201 Created
  (empty body)
       │
       ▼
  React SPA shows success message
```

---

## 9. Service Layer

```
┌─────────────────────────────────────────────────────────────────┐
│                      SERVICE LAYER                               │
│                                                                  │
│  ┌─── Admission ────────────────────────────────────────────┐   │
│  │  ApplicationAdmission    — create apps, set user         │   │
│  │  ApplicationManager      — manage app lifecycle          │   │
│  │  AdmissionNotifier       — notify subscribers (limit:100)│   │
│  │  AdmissionStatistics     — admission analytics           │   │
│  │  ApplicationData         — application data helpers      │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Interviews ───────────────────────────────────────────┐   │
│  │  InterviewManager        — schedule, assign interviews   │   │
│  │  InterviewCounter        — count/stats                   │   │
│  │  InterviewNotificationMgr — send reminders               │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Users & Auth ─────────────────────────────────────────┐   │
│  │  UserRegistration        — new user codes, activation    │   │
│  │  UserService             — user data helpers             │   │
│  │  PasswordManager         — reset codes, password setting │   │
│  │  LoginManager            — login helpers                 │   │
│  │  RoleManager             — role assignment               │   │
│  │  AccessControlService    — permission checking (cached)  │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Communication ────────────────────────────────────────┐   │
│  │  EmailSender             — outbound email service        │   │
│  │  SlackMessenger          — Slack webhook integration     │   │
│  │  SlackMailer             — Slack-formatted notifications │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Surveys ──────────────────────────────────────────────┐   │
│  │  SurveyManager           — create/manage surveys         │   │
│  │  SurveyNotifier          — send survey links             │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Infrastructure ───────────────────────────────────────┐   │
│  │  FileUploader            — images, receipts, signatures  │   │
│  │  GeoLocation             — department lat/lng            │   │
│  │  LogService              — structured logging            │   │
│  │  CompanyEmailMaker       — @vektorprogrammet.no emails   │   │
│  │  FilterService           — query filtering               │   │
│  │  Sorter                  — collection sorting            │   │
│  │  ContentModeManager      — CMS mode switching            │   │
│  │  BetaRedirecter          — beta/v2 redirects             │   │
│  │  SlugMaker               — URL slug generation           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─── Teams ────────────────────────────────────────────────┐   │
│  │  TeamMembershipService   — join/leave teams              │   │
│  │  UserGroupCollectionMgr  — user group management         │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 10. Frontend Integration

```
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND ARCHITECTURE                          │
│                                                                  │
│  ┌─── v1: Twig SSR (legacy, full-featured) ─────────────────┐  │
│  │                                                            │  │
│  │  templates/                                                │  │
│  │  ├── base.html.twig          (master layout)               │  │
│  │  ├── home/                   (public pages)                │  │
│  │  ├── admission/              (application forms)           │  │
│  │  ├── control_panel/          (admin dashboard)             │  │
│  │  ├── interview/              (interview management)        │  │
│  │  ├── survey/                 (survey system)               │  │
│  │  └── ...                     (~200+ templates)             │  │
│  │                                                            │  │
│  │  Built with: Bootstrap 4, jQuery, Vite 5                   │  │
│  │  Auth: Session cookies (form_login)                        │  │
│  │  Routes: ~193 controller routes                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─── v2: React SPA (new public homepage) ──────────────────┐  │
│  │                                                            │  │
│  │  ../v2/homepage/                                           │  │
│  │  ├── src/                    (React components)            │  │
│  │  └── ...                                                   │  │
│  │                                                            │  │
│  │  Consumes: API Platform endpoints (JWT)                    │  │
│  │  Auth: POST /api/login → JWT token                         │  │
│  │  Data: GET /api/departments, /api/articles, etc.          │  │
│  │  Actions: POST /api/applications, /api/contact_messages   │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─── Asset Pipeline ───────────────────────────────────────┐   │
│  │  Vite 5 (vite.config.js)                                  │   │
│  │    Entry points → bundled JS/CSS                           │   │
│  │    Dev: HMR server                                         │   │
│  │    Prod: hashed bundles in public/build/                   │   │
│  │    Integration: pentatrion/vite-bundle for Twig            │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

---

## Summary

| Dimension | Detail |
|-----------|--------|
| **Framework** | Symfony 6.4, PHP 8.1 |
| **API Layer** | Dual: API Platform 3.4 (JWT, stateless) + FOS REST (session) |
| **Entities** | 40+ Doctrine ORM entities |
| **API Resources** | 9 DTOs + 12 entity-based resources |
| **Controllers** | ~61 Twig controllers |
| **Services** | 32 business logic services |
| **Events** | 14 event subscribers, custom domain events |
| **Auth** | JWT (3600s TTL) + session + remember-me (1yr) |
| **Roles** | USER → TEAM_MEMBER → TEAM_LEADER → ADMIN |
| **Frontend** | Twig SSR (v1) + React SPA (v2), Vite 5 build |
| **Testing** | 536 tests, PHPStan L1, PHP-CS-Fixer |
