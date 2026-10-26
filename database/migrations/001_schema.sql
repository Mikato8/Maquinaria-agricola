-- ===========================================================================
-- AgroMáquina MX · esquema base
-- PostgreSQL 15+ (probado en Neon PostgreSQL 18)
-- ===========================================================================

create extension if not exists citext;
create extension if not exists pg_trgm;
create extension if not exists unaccent;

-- ---------------------------------------------------------------------------
-- Geografía
-- ---------------------------------------------------------------------------
create table if not exists states (
    code varchar(5) primary key,
    name text not null,
    region text not null default 'Centro'
);

-- ---------------------------------------------------------------------------
-- Cuentas
-- ---------------------------------------------------------------------------
create table if not exists users (
    id bigserial primary key,
    name text not null,
    email citext not null unique,
    password_hash text not null,
    role text not null default 'productor' check (role in ('productor', 'tecnico', 'taller', 'editor', 'admin')),
    status text not null default 'pendiente' check (status in ('pendiente', 'verificado', 'suspendido')),
    headline text not null default '',
    workshop_name text not null default '',
    city text not null default '',
    state_code varchar(5) references states(code),
    phone text not null default '',
    bio text not null default '',
    avatar_url text,
    proof_path text,
    verification_method text not null default 'manual' check (verification_method in ('cedula', 'taller', 'manual', 'admin')),
    verification_note text not null default '',
    verified_at timestamptz,
    terms_accepted_at timestamptz,
    email_verified boolean not null default false,
    last_login_at timestamptz,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    deleted_at timestamptz
);

create index if not exists users_status_idx on users (status) where deleted_at is null;
create index if not exists users_role_idx on users (role);

-- ---------------------------------------------------------------------------
-- Marcas y máquinas autopropulsadas
-- ---------------------------------------------------------------------------
create table if not exists brands (
    id serial primary key,
    slug text not null unique,
    name text not null,
    country text not null default '',
    segment text not null default 'mixto' check (segment in ('tractor', 'cosechadora', 'pulverizadora', 'implemento', 'mixto')),
    website text not null default '',
    in_mexico boolean not null default true,
    notes text not null default ''
);

create table if not exists machines (
    id bigserial primary key,
    slug text not null unique,
    brand_id int not null references brands(id) on delete restrict,
    model text not null,
    name text not null,
    category text not null check (category in ('tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'ordeno', 'riego', 'transporte')),
    traction text not null default '4WD' check (traction in ('2WD', '4WD', 'oruga', 'n/a')),
    power_hp numeric(7, 1),
    pto_hp numeric(7, 1),
    engine text not null default '',
    displacement_l numeric(5, 2),
    cylinders int,
    aspiration text not null default '' check (aspiration in ('', 'atmosferico', 'turbo', 'turbo_intercooler')),
    transmission text not null default '',
    gears int,
    pto_rpm int default 540,
    hydraulic_flow_lpm numeric(6, 1),
    lift_capacity_kg int,
    weight_kg int,
    fuel_tank_l numeric(6, 1),
    turning_radius_m numeric(4, 2),
    year_from int,
    year_to int,
    price_min_mxn numeric(12, 2),
    price_max_mxn numeric(12, 2),
    price_note text not null default '',
    availability text not null default 'Distribución nacional',
    summary text not null default '',
    description text not null default '',
    features text[] not null default '{}',
    common_uses text[] not null default '{}',
    maintenance_tip text not null default '',
    image_url text,
    views int not null default 0,
    status text not null default 'publicado' check (status in ('publicado', 'borrador', 'archivado')),
    is_demo boolean not null default false,
    search_vector tsvector generated always as (
        to_tsvector('spanish',
            coalesce(name, '') || ' ' || coalesce(model, '') || ' ' || coalesce(summary, '') || ' ' ||
            coalesce(description, '') || ' ' || coalesce(engine, '') || ' ' || coalesce(transmission, ''))
    ) stored,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create index if not exists machines_category_idx on machines (category, status);
create index if not exists machines_brand_idx on machines (brand_id);
create index if not exists machines_power_idx on machines (power_hp);
create index if not exists machines_search_idx on machines using gin (search_vector);
create index if not exists machines_name_trgm_idx on machines using gin (name gin_trgm_ops);

-- ---------------------------------------------------------------------------
-- Implementos y aditamentos
-- ---------------------------------------------------------------------------
create table if not exists implements (
    id bigserial primary key,
    slug text not null unique,
    name text not null,
    category text not null check (category in ('labranza', 'siembra', 'fertilizacion', 'proteccion', 'cosecha', 'forraje', 'transporte', 'riego', 'postcosecha', 'jardin')),
    function text not null default '',
    power_min_hp int not null default 0,
    power_max_hp int,
    working_width_m numeric(5, 2),
    working_depth_cm numeric(5, 1),
    weight_kg int,
    draft_factor numeric(5, 1) not null default 0,
    capacity text not null default '',
    hitch text not null default '3 puntos' check (hitch in ('3 puntos', 'arrastre', 'semi-montado', 'manual')),
    required_flow_lpm numeric(6, 1),
    min_lift_kg int,
    drive_required text not null default 'cualquiera' check (drive_required in ('cualquiera', '4WD', '2WD')),
    soil_types text[] not null default '{}',
    suitable_crops text[] not null default '{}',
    price_min_mxn numeric(12, 2),
    price_max_mxn numeric(12, 2),
    summary text not null default '',
    description text not null default '',
    operation text not null default '',
    calibration_tip text not null default '',
    safety text not null default '',
    maintenance text not null default '',
    image_url text,
    views int not null default 0,
    status text not null default 'publicado' check (status in ('publicado', 'borrador', 'archivado')),
    is_demo boolean not null default false,
    search_vector tsvector generated always as (
        to_tsvector('spanish',
            coalesce(name, '') || ' ' || coalesce(function, '') || ' ' || coalesce(summary, '') || ' ' ||
            coalesce(description, '') || ' ' || coalesce(operation, ''))
    ) stored,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create index if not exists implements_category_idx on implements (category, status);
create index if not exists implements_search_idx on implements using gin (search_vector);

-- ---------------------------------------------------------------------------
-- Fallas, diagnóstico y mantenimiento
-- ---------------------------------------------------------------------------
create table if not exists faults (
    id bigserial primary key,
    slug text not null unique,
    title text not null,
    machine_category text not null default 'tractor'
        check (machine_category in ('tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'ordeno', 'implemento', 'general')),
    system text not null
        check (system in ('motor', 'combustible', 'transmision', 'hidraulico', 'electrico', 'frenos', 'direccion',
                          'embrague', 'carga', 'enfriamiento', 'aftertreatment', 'neumaticos', 'cabina', 'electronica', 'implemento')),
    severity text not null default 'media' check (severity in ('leve', 'media', 'alta', 'critica')),
    frequency text not null default 'comun' check (frequency in ('comun', 'ocasional', 'rara')),
    symptom text not null default '',
    causes jsonb not null default '[]'::jsonb,
    steps jsonb not null default '[]'::jsonb,
    tools text[] not null default '{}',
    parts text[] not null default '{}',
    cost_min_mxn numeric(10, 2),
    cost_max_mxn numeric(10, 2),
    time_hours numeric(5, 1),
    difficulty int not null default 3 check (difficulty between 1 and 5),
    safety text not null default '',
    prevention text not null default '',
    when_to_stop text not null default '',
    references_text text not null default '',
    views int not null default 0,
    status text not null default 'publicado' check (status in ('publicado', 'borrador')),
    is_demo boolean not null default false,
    search_vector tsvector generated always as (
        to_tsvector('spanish',
            coalesce(title, '') || ' ' || coalesce(symptom, '') || ' ' ||
            coalesce(safety, '') || ' ' || coalesce(prevention, ''))
    ) stored,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create index if not exists faults_system_idx on faults (system, machine_category);
create index if not exists faults_search_idx on faults using gin (search_vector);

create table if not exists maintenance_tasks (
    id serial primary key,
    machine_category text not null default 'tractor'
        check (machine_category in ('tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'tractor_compacto')),
    interval_hours int not null,
    interval_label text not null default '',
    system text not null default 'general',
    task text not null,
    detail text not null default '',
    parts text[] not null default '{}',
    sort_order int not null default 100,
    is_demo boolean not null default false
);

create index if not exists maintenance_category_idx on maintenance_tasks (machine_category, interval_hours);

create table if not exists dtc_codes (
    id serial primary key,
    code text not null,
    system text not null default 'motor',
    brand_scope text not null default 'J1939 / genérico',
    title text not null,
    description text not null default '',
    causes text[] not null default '{}',
    action text not null default '',
    severity text not null default 'media' check (severity in ('leve', 'media', 'alta', 'critica')),
    notes text not null default '',
    is_demo boolean not null default false,
    unique (code, brand_scope)
);

-- ---------------------------------------------------------------------------
-- Guías editoriales largas
-- ---------------------------------------------------------------------------
create table if not exists guides (
    id bigserial primary key,
    slug text not null unique,
    title text not null,
    category text not null default 'fundamentos'
        check (category in ('fundamentos', 'mantenimiento', 'precision', 'compra', 'seguridad', 'taller')),
    summary text not null default '',
    body text not null default '',
    level text not null default 'introductorio' check (level in ('introductorio', 'intermedio', 'avanzado')),
    reading_min int not null default 8,
    author text not null default 'Equipo editorial',
    featured boolean not null default false,
    published_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    is_demo boolean not null default false,
    search_vector tsvector generated always as (
        to_tsvector('spanish', coalesce(title, '') || ' ' || coalesce(summary, '') || ' ' || coalesce(body, ''))
    ) stored
);

create index if not exists guides_category_idx on guides (category);
create index if not exists guides_search_idx on guides using gin (search_vector);

-- ---------------------------------------------------------------------------
-- Software, hardware, datos y modelos (agricultura de precisión)
-- ---------------------------------------------------------------------------
create table if not exists tech_resources (
    id bigserial primary key,
    slug text not null unique,
    name text not null,
    kind text not null default 'software' check (kind in ('software', 'hardware', 'dataset', 'modelo', 'espacio', 'norma')),
    category text not null default 'gestion'
        check (category in ('guiado', 'telemetria', 'gestion', 'isobus', 'vision', 'nlp', 'teledeteccion', 'datos', 'robotica', 'riego', 'taller')),
    vendor text not null default '',
    license text not null default '',
    platform text not null default '',
    size_label text not null default '',
    price_note text not null default '',
    mexico_fit text not null default '',
    description text not null default '',
    features text[] not null default '{}',
    requirements text not null default '',
    url text not null default '',
    docs_url text not null default '',
    verified_on date,
    sort_order int not null default 100,
    is_demo boolean not null default false
);

create index if not exists tech_kind_idx on tech_resources (kind, category);

-- ---------------------------------------------------------------------------
-- Proveedores, refacciones y servicios
-- ---------------------------------------------------------------------------
create table if not exists providers (
    id serial primary key,
    slug text not null unique,
    name text not null,
    type text not null default 'distribuidor'
        check (type in ('distribuidor', 'refacciones', 'taller', 'renta', 'financiamiento', 'capacitacion', 'fabricante')),
    brands text[] not null default '{}',
    states text[] not null default '{}',
    city text not null default '',
    address text not null default '',
    phone text not null default '',
    whatsapp text not null default '',
    email text not null default '',
    website text not null default '',
    hours text not null default '',
    services text[] not null default '{}',
    notes text not null default '',
    verified_on date,
    is_demo boolean not null default false
);

-- ---------------------------------------------------------------------------
-- Casos de taller (comunidad)
-- ---------------------------------------------------------------------------
create table if not exists workshop_cases (
    id bigserial primary key,
    slug text not null unique,
    folio text not null unique,
    author_id bigint not null references users(id) on delete restrict,
    title text not null,
    summary text not null default '',
    machine_category text not null default 'tractor'
        check (machine_category in ('tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'ordeno', 'implemento', 'general')),
    machine_label text not null default '',
    machine_id bigint references machines(id) on delete set null,
    state_code varchar(5) references states(code),
    symptom text not null default '',
    diagnosis text not null default '',
    repair text not null default '',
    parts text not null default '',
    cost_mxn numeric(10, 2),
    time_hours numeric(6, 1),
    difficulty int not null default 3 check (difficulty between 1 and 5),
    outcome text not null default 'resuelto' check (outcome in ('resuelto', 'parcial', 'no_resuelto')),
    safety text not null default '',
    tips text not null default '',
    status text not null default 'borrador' check (status in ('borrador', 'en_revision', 'cambios_solicitados', 'publicado', 'rechazado', 'archivado')),
    featured boolean not null default false,
    views int not null default 0,
    is_demo boolean not null default false,
    published_at timestamptz,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    search_vector tsvector generated always as (
        to_tsvector('spanish',
            coalesce(title, '') || ' ' || coalesce(summary, '') || ' ' || coalesce(symptom, '') || ' ' ||
            coalesce(diagnosis, '') || ' ' || coalesce(repair, ''))
    ) stored
);

create index if not exists workshop_status_idx on workshop_cases (status, published_at desc);
create index if not exists workshop_category_idx on workshop_cases (machine_category);
create index if not exists workshop_search_idx on workshop_cases using gin (search_vector);

create table if not exists workshop_case_images (
    id bigserial primary key,
    case_id bigint not null references workshop_cases(id) on delete cascade,
    url text not null,
    caption text not null default '',
    sort_order int not null default 0,
    created_at timestamptz not null default now()
);

create index if not exists workshop_images_case_idx on workshop_case_images (case_id);

create table if not exists comments (
    id bigserial primary key,
    target_type text not null default 'taller' check (target_type in ('taller')),
    target_id bigint not null,
    user_id bigint not null references users(id) on delete cascade,
    body text not null,
    status text not null default 'visible' check (status in ('visible', 'oculto')),
    created_at timestamptz not null default now()
);

create index if not exists comments_target_idx on comments (target_type, target_id, status);

create table if not exists reactions (
    target_type text not null default 'taller' check (target_type in ('taller')),
    target_id bigint not null,
    user_id bigint not null references users(id) on delete cascade,
    created_at timestamptz not null default now(),
    primary key (target_type, target_id, user_id)
);

create table if not exists bookmarks (
    target_type text not null check (target_type in ('taller', 'maquina', 'falla', 'guia', 'implemento', 'recurso')),
    target_id bigint not null,
    user_id bigint not null references users(id) on delete cascade,
    created_at timestamptz not null default now(),
    primary key (target_type, target_id, user_id)
);

create table if not exists reviews (
    id bigserial primary key,
    target_type text not null default 'taller' check (target_type in ('taller')),
    target_id bigint not null,
    reviewer_id bigint references users(id) on delete set null,
    decision text not null check (decision in ('publicar', 'cambios', 'rechazar')),
    notes text not null default '',
    created_at timestamptz not null default now()
);

create index if not exists reviews_target_idx on reviews (target_type, target_id);

create table if not exists audit_log (
    id bigserial primary key,
    actor_id bigint references users(id) on delete set null,
    action text not null,
    target_type text not null default '',
    target_id bigint,
    detail jsonb not null default '{}'::jsonb,
    ip text not null default '',
    created_at timestamptz not null default now()
);

create index if not exists audit_created_idx on audit_log (created_at desc);

-- ---------------------------------------------------------------------------
-- Glosario y contenido de apoyo
-- ---------------------------------------------------------------------------
create table if not exists glossary (
    id serial primary key,
    term text not null,
    slug text not null unique,
    category text not null default 'técnico'
        check (category in ('técnico', 'agronómico', 'precisión', 'comercial', 'seguridad', 'normativa')),
    definition text not null,
    see_also text[] not null default '{}',
    sort_order int not null default 100
);

-- ---------------------------------------------------------------------------
-- Mensajes de contacto y solicitudes de taller
-- ---------------------------------------------------------------------------
create table if not exists inquiries (
    id bigserial primary key,
    name text not null,
    email citext not null,
    phone text not null default '',
    state_code varchar(5) references states(code),
    subject text not null default '',
    message text not null,
    kind text not null default 'contacto' check (kind in ('contacto', 'taller', 'proveedor', 'sugerencia')),
    handled boolean not null default false,
    created_at timestamptz not null default now()
);

-- ---------------------------------------------------------------------------
-- Intentos de acceso (control de fuerza bruta)
-- ---------------------------------------------------------------------------
create table if not exists login_attempts (
    id bigserial primary key,
    email citext not null,
    ip inet,
    successful boolean not null default false,
    created_at timestamptz not null default now()
);

create index if not exists login_attempts_created_idx on login_attempts (created_at desc);
create index if not exists login_attempts_email_idx on login_attempts (email);

-- ---------------------------------------------------------------------------
-- Recuperación de contraseña
-- ---------------------------------------------------------------------------
create table if not exists password_resets (
    id bigserial primary key,
    user_id bigint not null references users(id) on delete cascade,
    token_hash text not null unique,
    expires_at timestamptz not null,
    used_at timestamptz,
    created_at timestamptz not null default now()
);

create index if not exists password_resets_user_idx on password_resets (user_id);
