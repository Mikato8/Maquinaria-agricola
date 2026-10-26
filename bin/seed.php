<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Core\Str;

/**
 * Convierte un array PHP en literal de arreglo de PostgreSQL.
 *
 * @param array<int, string> $values
 */
function pg_text_array(array $values): string
{
    if ($values === []) {
        return '{}';
    }
    $escaped = array_map(static function (string $value): string {
        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }, $values);
    return '{' . implode(',', $escaped) . '}';
}

function pg_json(array $value): string
{
    return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
}

$options = getopt('', ['demo', 'force', 'no-demo']);
$withDemo = !isset($options['no-demo']);
$force = isset($options['force']);
$catalog = require BASE_PATH . '/database/seeds/catalog.php';
$implementsData = require BASE_PATH . '/database/seeds/implements.php';
$faultsData = require BASE_PATH . '/database/seeds/faults.php';
$repairData = require BASE_PATH . '/database/seeds/maintenance.php';
$techData = require BASE_PATH . '/database/seeds/tech.php';
$providersData = require BASE_PATH . '/database/seeds/providers.php';
$glossaryData = require BASE_PATH . '/database/seeds/glossary.php';
$guidesData = require BASE_PATH . '/database/seeds/guides.php';

echo "→ Estados…\n";
foreach ($catalog['states'] as [$code, $name, $region]) {
    Database::execute(
        'insert into states (code, name, region) values ($1, $2, $3)
         on conflict (code) do update set name = excluded.name, region = excluded.region',
        [$code, $name, $region]
    );
}
echo '  ✓ ' . count($catalog['states']) . " estados\n";

echo "→ Marcas…\n";
$brandIds = [];
foreach ($catalog['brands'] as [$slug, $name, $country, $segment, $website, $inMexico, $notes]) {
    $row = Database::insert(
        'insert into brands (slug, name, country, segment, website, in_mexico, notes)
         values ($1, $2, $3, $4, $5, $6, $7)
         on conflict (slug) do update set name = excluded.name, country = excluded.country, segment = excluded.segment,
             website = excluded.website, in_mexico = excluded.in_mexico, notes = excluded.notes
         returning id',
        [$slug, $name, $country, $segment, $website, $inMexico, $notes]
    );
    $brandIds[$slug] = (int) ($row['id'] ?? Database::value('select id from brands where slug = $1', [$slug]));
}
echo '  ✓ ' . count($brandIds) . " marcas\n";

echo "→ Máquinas…\n";
$machineIds = [];
foreach ($catalog['machines'] as $machine) {
    $row = Database::insert(
        'insert into machines (slug, brand_id, model, name, category, traction, power_hp, pto_hp, engine, displacement_l,
            cylinders, aspiration, transmission, gears, pto_rpm, hydraulic_flow_lpm, lift_capacity_kg, weight_kg,
            fuel_tank_l, turning_radius_m, year_from, year_to, price_min_mxn, price_max_mxn, price_note, availability,
            summary, description, features, common_uses, maintenance_tip, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,$24,$25,$26,
                 $27,$28,$29::text[],$30::text[],$31,true)
         on conflict (slug) do update set brand_id = excluded.brand_id, model = excluded.model, name = excluded.name,
             category = excluded.category, traction = excluded.traction, power_hp = excluded.power_hp,
             pto_hp = excluded.pto_hp, engine = excluded.engine, displacement_l = excluded.displacement_l,
             cylinders = excluded.cylinders, aspiration = excluded.aspiration, transmission = excluded.transmission,
             gears = excluded.gears, pto_rpm = excluded.pto_rpm, hydraulic_flow_lpm = excluded.hydraulic_flow_lpm,
             lift_capacity_kg = excluded.lift_capacity_kg, weight_kg = excluded.weight_kg,
             fuel_tank_l = excluded.fuel_tank_l, turning_radius_m = excluded.turning_radius_m,
             year_from = excluded.year_from, year_to = excluded.year_to, price_min_mxn = excluded.price_min_mxn,
             price_max_mxn = excluded.price_max_mxn, price_note = excluded.price_note, availability = excluded.availability,
             summary = excluded.summary, description = excluded.description, features = excluded.features,
             common_uses = excluded.common_uses, maintenance_tip = excluded.maintenance_tip, updated_at = now()
         returning id',
        [
            $machine['slug'], $brandIds[$machine['brand']], $machine['model'], $machine['name'], $machine['category'],
            $machine['traction'], $machine['power_hp'], $machine['pto_hp'], $machine['engine'], $machine['displacement_l'],
            $machine['cylinders'], $machine['aspiration'], $machine['transmission'], $machine['gears'], $machine['pto_rpm'],
            $machine['hydraulic_flow_lpm'], $machine['lift_capacity_kg'], $machine['weight_kg'], $machine['fuel_tank_l'],
            $machine['turning_radius_m'], $machine['year_from'], $machine['year_to'], $machine['price_min_mxn'],
            $machine['price_max_mxn'], $machine['price_note'], $machine['availability'], $machine['summary'],
            $machine['description'], pg_text_array($machine['features']), pg_text_array($machine['common_uses']),
            $machine['maintenance_tip'],
        ]
    );
    $machineIds[$machine['slug']] = (int) ($row['id'] ?? Database::value('select id from machines where slug = $1', [$machine['slug']]));
}
echo '  ✓ ' . count($machineIds) . " máquinas\n";

echo "→ Implementos…\n";
$implementIds = [];
foreach ($implementsData as $implement) {
    $row = Database::insert(
        'insert into implements (slug, name, category, function, power_min_hp, power_max_hp, working_width_m,
            working_depth_cm, weight_kg, draft_factor, capacity, hitch, required_flow_lpm, min_lift_kg, drive_required,
            soil_types, suitable_crops, price_min_mxn, price_max_mxn, summary, description, operation, calibration_tip,
            safety, maintenance, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16::text[],$17::text[],$18,$19,$20,$21,$22,$23,$24,$25,true)
         on conflict (slug) do update set name = excluded.name, category = excluded.category, function = excluded.function,
             power_min_hp = excluded.power_min_hp, power_max_hp = excluded.power_max_hp,
             working_width_m = excluded.working_width_m, working_depth_cm = excluded.working_depth_cm,
             weight_kg = excluded.weight_kg, draft_factor = excluded.draft_factor, capacity = excluded.capacity,
             hitch = excluded.hitch, required_flow_lpm = excluded.required_flow_lpm, min_lift_kg = excluded.min_lift_kg,
             drive_required = excluded.drive_required, soil_types = excluded.soil_types,
             suitable_crops = excluded.suitable_crops, price_min_mxn = excluded.price_min_mxn,
             price_max_mxn = excluded.price_max_mxn, summary = excluded.summary, description = excluded.description,
             operation = excluded.operation, calibration_tip = excluded.calibration_tip, safety = excluded.safety,
             maintenance = excluded.maintenance, updated_at = now()
         returning id',
        [
            $implement['slug'], $implement['name'], $implement['category'], $implement['function'],
            $implement['power_min_hp'], $implement['power_max_hp'], $implement['working_width_m'],
            $implement['working_depth_cm'], $implement['weight_kg'], $implement['draft_factor'],
            $implement['capacity'], $implement['hitch'], $implement['required_flow_lpm'], $implement['min_lift_kg'],
            $implement['drive_required'], pg_text_array($implement['soil_types']), pg_text_array($implement['suitable_crops']),
            $implement['price_min_mxn'], $implement['price_max_mxn'], $implement['summary'], $implement['description'],
            $implement['operation'], $implement['calibration_tip'], $implement['safety'], $implement['maintenance'],
        ]
    );
    $implementIds[$implement['slug']] = (int) ($row['id'] ?? Database::value('select id from implements where slug = $1', [$implement['slug']]));
}
echo '  ✓ ' . count($implementIds) . " implementos\n";

echo "→ Fallas…\n";
$faultIds = [];
foreach ($faultsData as $fault) {
    $row = Database::insert(
        'insert into faults (slug, title, machine_category, system, severity, frequency, symptom, causes, steps,
            tools, parts, cost_min_mxn, cost_max_mxn, time_hours, difficulty, safety, prevention, when_to_stop,
            references_text, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7,$8::jsonb,$9::jsonb,$10::text[],$11::text[],$12,$13,$14,$15,$16,$17,$18,$19,true)
         on conflict (slug) do update set title = excluded.title, machine_category = excluded.machine_category,
             system = excluded.system, severity = excluded.severity, frequency = excluded.frequency,
             symptom = excluded.symptom, causes = excluded.causes, steps = excluded.steps, tools = excluded.tools,
             parts = excluded.parts, cost_min_mxn = excluded.cost_min_mxn, cost_max_mxn = excluded.cost_max_mxn,
             time_hours = excluded.time_hours, difficulty = excluded.difficulty, safety = excluded.safety,
             prevention = excluded.prevention, when_to_stop = excluded.when_to_stop,
             references_text = excluded.references_text, updated_at = now()
         returning id',
        [
            $fault['slug'], $fault['title'], $fault['machine_category'], $fault['system'], $fault['severity'],
            $fault['frequency'], $fault['symptom'], pg_json($fault['causes']), pg_json($fault['steps']),
            pg_text_array($fault['tools']), pg_text_array($fault['parts']), $fault['cost_min_mxn'],
            $fault['cost_max_mxn'], $fault['time_hours'], $fault['difficulty'], $fault['safety'],
            $fault['prevention'], $fault['when_to_stop'], $fault['references_text'],
        ]
    );
    $faultIds[$fault['slug']] = (int) ($row['id'] ?? Database::value('select id from faults where slug = $1', [$fault['slug']]));
}
echo '  ✓ ' . count($faultIds) . " fallas\n";

echo "→ Mantenimiento y códigos…\n";
Database::execute('delete from maintenance_tasks');
$position = 0;
foreach ($repairData['maintenance'] as $task) {
    [$category, $hours, $label, $system, $taskText, $detail, $parts] = $task;
    $position += 10;
    Database::execute(
        'insert into maintenance_tasks (machine_category, interval_hours, interval_label, system, task, detail, parts, sort_order, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7::text[],$8,true)',
        [$category, $hours, $label, $system, $taskText, $detail, pg_text_array($parts), $position]
    );
}
Database::execute('delete from dtc_codes');
foreach ($repairData['dtc'] as $code) {
    [$codeText, $system, $scope, $title, $description, $causes, $action, $severity, $notes] = $code;
    Database::execute(
        'insert into dtc_codes (code, system, brand_scope, title, description, causes, action, severity, notes, is_demo)
         values ($1,$2,$3,$4,$5,$6::text[],$7,$8,$9,true)
         on conflict (code, brand_scope) do update set system = excluded.system, title = excluded.title,
             description = excluded.description, causes = excluded.causes, action = excluded.action,
             severity = excluded.severity, notes = excluded.notes',
        [$codeText, $system, $scope, $title, $description, pg_text_array($causes), $action, $severity, $notes]
    );
}
echo '  ✓ ' . count($repairData['maintenance']) . ' tareas de mantenimiento, ' . count($repairData['dtc']) . " códigos\n";

echo "→ Tecnología…\n";
$techIds = [];
foreach ($techData as $resource) {
    $row = Database::insert(
        'insert into tech_resources (slug, name, kind, category, vendor, license, platform, size_label, price_note,
            mexico_fit, description, features, requirements, url, docs_url, verified_on, sort_order, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12::text[],$13,$14,$15,$16,$17,true)
         on conflict (slug) do update set name = excluded.name, kind = excluded.kind, category = excluded.category,
             vendor = excluded.vendor, license = excluded.license, platform = excluded.platform,
             size_label = excluded.size_label, price_note = excluded.price_note, mexico_fit = excluded.mexico_fit,
             description = excluded.description, features = excluded.features, requirements = excluded.requirements,
             url = excluded.url, docs_url = excluded.docs_url, verified_on = excluded.verified_on,
             sort_order = excluded.sort_order
         returning id',
        [
            $resource['slug'], $resource['name'], $resource['kind'], $resource['category'], $resource['vendor'],
            $resource['license'], $resource['platform'], $resource['size_label'], $resource['price_note'],
            $resource['mexico_fit'], $resource['description'], pg_text_array($resource['features']),
            $resource['requirements'], $resource['url'], $resource['docs_url'], $resource['verified_on'],
            $resource['sort_order'],
        ]
    );
    $techIds[$resource['slug']] = (int) ($row['id'] ?? 0);
}
echo '  ✓ ' . count($techIds) . " recursos\n";

echo "→ Proveedores…\n";
$providerIds = [];
foreach ($providersData as $provider) {
    $row = Database::insert(
        'insert into providers (slug, name, type, brands, states, city, address, phone, whatsapp, email, website,
            hours, services, notes, verified_on, is_demo)
         values ($1,$2,$3,$4::text[],$5::text[],$6,$7,$8,$9,$10,$11,$12,$13::text[],$14,$15,$16)
         on conflict (slug) do update set name = excluded.name, type = excluded.type, brands = excluded.brands,
             states = excluded.states, city = excluded.city, address = excluded.address, phone = excluded.phone,
             whatsapp = excluded.whatsapp, email = excluded.email, website = excluded.website, hours = excluded.hours,
             services = excluded.services, notes = excluded.notes, verified_on = excluded.verified_on
         returning id',
        [
            $provider['slug'], $provider['name'], $provider['type'], pg_text_array($provider['brands']),
            pg_text_array($provider['states']), $provider['city'], $provider['address'], $provider['phone'],
            $provider['whatsapp'], $provider['email'], $provider['website'], $provider['hours'],
            pg_text_array($provider['services']), $provider['notes'], $provider['verified_on'], $provider['is_demo'],
        ]
    );
    $providerIds[$provider['slug']] = (int) ($row['id'] ?? 0);
}
echo '  ✓ ' . count($providerIds) . " proveedores\n";

echo "→ Glosario…\n";
$glossaryIds = [];
foreach ($glossaryData as [$term, $slug, $category, $definition, $seeAlso, $sortOrder]) {
    $row = Database::insert(
        'insert into glossary (term, slug, category, definition, see_also, sort_order)
         values ($1,$2,$3,$4,$5::text[],$6)
         on conflict (slug) do update set term = excluded.term, category = excluded.category,
             definition = excluded.definition, see_also = excluded.see_also, sort_order = excluded.sort_order
         returning id',
        [$term, $slug, $category, $definition, pg_text_array($seeAlso), $sortOrder]
    );
    $glossaryIds[$slug] = (int) ($row['id'] ?? 0);
}
echo '  ✓ ' . count($glossaryIds) . " términos\n";

echo "→ Guías…\n";
$guideIds = [];
foreach ($guidesData as $guide) {
    $row = Database::insert(
        'insert into guides (slug, title, category, summary, body, level, reading_min, author, featured, published_at, updated_at, is_demo)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$10,true)
         on conflict (slug) do update set title = excluded.title, category = excluded.category, summary = excluded.summary,
             body = excluded.body, level = excluded.level, reading_min = excluded.reading_min,
             author = excluded.author, featured = excluded.featured, published_at = excluded.published_at,
             updated_at = excluded.updated_at
         returning id',
        [$guide['slug'], $guide['title'], $guide['category'], $guide['summary'], $guide['body'], $guide['level'],
         $guide['reading_min'], 'Equipo editorial', $guide['featured'], $guide['published_at']]
    );
    $guideIds[$guide['slug']] = (int) ($row['id'] ?? 0);
}
echo '  ✓ ' . count($guideIds) . " guías\n";

if (!$withDemo) {
    echo "\nListo. Catálogo sembrado sin contenido demostrativo.\n";
    exit(0);
}

$demo = require BASE_PATH . '/database/seeds/demo.php';
$existing = (int) Database::value('select count(*) from workshop_cases where is_demo = true');
if ($existing > 0 && !$force) {
    echo "\n· El contenido demostrativo ya existe ({$existing} casos). Usa --force para recrearlo.\n";
    exit(0);
}

if ($force) {
    echo "→ Eliminando contenido demostrativo anterior…\n";
    Database::execute("delete from reactions where target_type = 'taller' and target_id in (select id from workshop_cases where is_demo = true)");
    Database::execute("delete from comments where target_type = 'taller' and target_id in (select id from workshop_cases where is_demo = true)");
    Database::execute("delete from reviews where target_type = 'taller' and target_id in (select id from workshop_cases where is_demo = true)");
    Database::execute('delete from workshop_case_images where case_id in (select id from workshop_cases where is_demo = true)');
    Database::execute('delete from workshop_cases where is_demo = true');
}

echo "→ Cuentas demostrativas…\n";
$userIds = [];
foreach ($demo['users'] as $user) {
    $email = Str::normalizeEmail($user['email']);
    $existingId = Database::value('select id from users where email = $1', [$email]);
    if ($existingId !== null) {
        $userIds[$user['key']] = (int) $existingId;
        continue;
    }
    $row = Database::insert(
        'insert into users (name, email, password_hash, role, status, headline, workshop_name, city, state_code,
            bio, verification_method, verified_at, terms_accepted_at, email_verified, created_at)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,now(),now(),true,now() - interval \'60 days\')
         returning id',
        [
            $user['name'], $email, password_hash($demo['password'], PASSWORD_DEFAULT), $user['role'], $user['status'],
            $user['headline'], $user['workshop_name'], $user['city'], $user['state'], $user['bio'], $user['method'],
        ]
    );
    $userIds[$user['key']] = (int) ($row['id'] ?? 0);
}
$editorId = $userIds['ana'] ?? null;

echo "→ Casos de taller…\n";
$caseIds = [];
$caseNumber = 0;
foreach ($demo['cases'] as $case) {
    $caseNumber++;
    $existingCase = (int) Database::value('select id from workshop_cases where slug = $1', [$case['slug']]);
    if ($existingCase > 0) {
        $caseIds[$case['slug']] = $existingCase;
        continue;
    }
    $publishedAt = date('c', strtotime('-' . (int) $case['published_days_ago'] . ' days'));
    $folio = 'TAL-' . date('Y') . '-' . str_pad((string) $caseNumber, 4, '0', STR_PAD_LEFT);
    $row = Database::insert(
        'insert into workshop_cases (slug, folio, author_id, title, summary, machine_category, machine_label,
            machine_id, state_code, symptom, diagnosis, repair, parts, cost_mxn, time_hours, difficulty, outcome,
            safety, tips, status, featured, is_demo, published_at, created_at)
         values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,\'publicado\',$20,true,$21,$21)
         returning id',
        [
            $case['slug'], $folio, $userIds[$case['author']], $case['title'], $case['summary'],
            $case['machine_category'], $case['machine_label'], $machineIds[$case['machine_slug']] ?? null,
            $case['state'], $case['symptom'], $case['diagnosis'], $case['repair'], $case['parts'],
            $case['cost'], $case['time'], $case['difficulty'], $case['outcome'], $case['safety'],
            $case['tips'], !empty($case['featured']), $publishedAt,
        ]
    );
    $caseId = (int) ($row['id'] ?? 0);
    $caseIds[$case['slug']] = $caseId;
    if ($editorId !== null && $caseId > 0) {
        Database::execute(
            "insert into reviews (target_type, target_id, reviewer_id, decision, notes, created_at)
             values ('taller', $1, $2, 'publicar', $3, now() - interval '5 days')",
            [$caseId, $editorId, 'Caso coherente, con síntomas, pruebas y costos verificables. Aprobado.']
        );
    }
}

echo "→ Comentarios y reacciones…\n";
foreach ($demo['comments'] as $comment) {
    $caseId = $caseIds[$comment['case']] ?? 0;
    $userId = $userIds[$comment['author']] ?? 0;
    if ($caseId === 0 || $userId === 0) {
        continue;
    }
    $exists = Database::value(
        "select 1 from comments where target_type = 'taller' and target_id = $1 and user_id = $2 limit 1",
        [$caseId, $userId]
    );
    if ($exists !== null) {
        continue;
    }
    Database::execute(
        "insert into comments (target_type, target_id, user_id, body, created_at) values ('taller', $1, $2, $3, now() - interval '3 days')",
        [$caseId, $userId, $comment['body']]
    );
}
foreach ($demo['reactions'] as $reaction) {
    $caseId = $caseIds[$reaction['case']] ?? 0;
    if ($caseId === 0) {
        continue;
    }
    foreach ($reaction['authors'] as $authorKey) {
        $userId = $userIds[$authorKey] ?? 0;
        if ($userId === 0) {
            continue;
        }
        Database::execute(
            "insert into reactions (target_type, target_id, user_id) values ('taller', $1, $2) on conflict do nothing",
            [$caseId, $userId]
        );
    }
}

echo '  ✓ ' . count(array_filter($caseIds)) . " casos, " . count($demo['comments']) . " comentarios\n";
echo "\nListo. Contraseña de las cuentas demostrativas: {$demo['password']}\n";
echo "  Técnico: juan@ejemplo.mx · Editor: editor@ejemplo.mx\n";
