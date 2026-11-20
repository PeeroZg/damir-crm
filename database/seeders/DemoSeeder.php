<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Enums\CompanyStatus;
use App\Enums\ContactStatus;
use App\Enums\DealStage;
use App\Enums\OfferStatus;
use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;
use App\Rules\Oib;
use App\Services\OfferService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Realistic, fully fictional demo data: a small Croatian software agency selling
 * web/app projects. Seeded with a fixed random seed so every reset looks the same.
 */
class DemoSeeder extends Seeder
{
    private const COMPANIES = [
        ['Jadran Logistika d.o.o.', 'Rijeka', 'Logistics'],
        ['Kvarner Soft d.o.o.', 'Opatija', 'Software'],
        ['Sljeme Gradnja d.o.o.', 'Zagreb', 'Construction'],
        ['Panonija Agro d.d.', 'Osijek', 'Agriculture'],
        ['Dalmatia Yachting d.o.o.', 'Split', 'Tourism'],
        ['Velebit Energija d.o.o.', 'Gospić', 'Energy'],
        ['Istra Vina d.o.o.', 'Poreč', 'Food & beverage'],
        ['Medvednica Clinic d.o.o.', 'Zagreb', 'Healthcare'],
        ['Zrinski Tisak d.o.o.', 'Varaždin', 'Printing'],
        ['Lika Drvo d.o.o.', 'Otočac', 'Manufacturing'],
        ['Neretva Fresh d.o.o.', 'Opuzen', 'Food & beverage'],
        ['Kupa Tekstil d.o.o.', 'Karlovac', 'Textile'],
        ['Plitvice Travel d.o.o.', 'Zagreb', 'Tourism'],
        ['Brodarica Marine d.o.o.', 'Šibenik', 'Marine'],
        ['Mura Metal d.o.o.', 'Čakovec', 'Manufacturing'],
        ['Bilogora Pekare d.o.o.', 'Bjelovar', 'Food & beverage'],
        ['Zagorje Termo d.o.o.', 'Krapina', 'Energy'],
        ['Učka Digital d.o.o.', 'Rijeka', 'Software'],
        ['Stone Bay Hotels d.o.o.', 'Dubrovnik', 'Hospitality'],
        ['Sava Fintech d.o.o.', 'Zagreb', 'Finance'],
        ['Cetina Outdoor d.o.o.', 'Omiš', 'Tourism'],
        ['Papuk Pharma d.o.o.', 'Požega', 'Healthcare'],
        ['Vransko Eco Farm d.o.o.', 'Biograd na Moru', 'Agriculture'],
        ['Brač Kamen d.o.o.', 'Supetar', 'Construction'],
    ];

    private const FIRST_NAMES = ['Ivan', 'Ana', 'Marko', 'Petra', 'Luka', 'Maja', 'Tomislav', 'Ivana', 'Josip', 'Kristina', 'Nikola', 'Martina', 'Filip', 'Lucija', 'Matej', 'Sara', 'Domagoj', 'Katarina', 'Ante', 'Marija', 'Hrvoje', 'Lana', 'Dario', 'Ema'];

    private const LAST_NAMES = ['Horvat', 'Kovačević', 'Babić', 'Marić', 'Jurić', 'Novak', 'Knežević', 'Vuković', 'Marković', 'Petrović', 'Matić', 'Tomić', 'Pavlović', 'Božić', 'Blažević', 'Grgić', 'Pavić', 'Radić', 'Perić', 'Šarić', 'Lovrić', 'Vidović'];

    private const POSITIONS = ['CEO', 'CTO', 'Procurement manager', 'Marketing director', 'Operations manager', 'IT manager', 'Head of sales', 'CFO', 'Office manager'];

    /** title => line items [description, qty, unit, unit price] */
    private const PROJECTS = [
        'Website redesign' => [['UX workshop & wireframes', 16, 'h', 55], ['UI design (8 templates)', 8, 'day', 380], ['Frontend & CMS development', 120, 'h', 45], ['Content migration', 1, 'pcs', 900]],
        'Online shop' => [['Discovery & product catalogue setup', 24, 'h', 50], ['Shop development', 160, 'h', 45], ['Card payment integration', 1, 'pcs', 1200], ['Hosting & SSL (12 months)', 12, 'month', 39]],
        'Booking system' => [['Requirements analysis', 20, 'h', 55], ['Booking engine development', 140, 'h', 48], ['Channel manager integration', 1, 'pcs', 2400], ['Staff training', 1, 'day', 450]],
        'Mobile app (iOS & Android)' => [['Product workshop', 2, 'day', 600], ['App design', 10, 'day', 380], ['Cross-platform development', 260, 'h', 50], ['Store publishing', 1, 'pcs', 350]],
        'ERP integration' => [['Integration analysis', 32, 'h', 60], ['API connector development', 90, 'h', 52], ['Data synchronisation jobs', 40, 'h', 52], ['Monitoring & alerting', 1, 'pcs', 800]],
        'Annual maintenance & hosting' => [['Managed hosting (12 months)', 12, 'month', 89], ['Support & maintenance hours', 60, 'h', 40], ['Security updates & backups', 12, 'month', 45]],
        'SEO & performance audit' => [['Technical SEO audit', 1, 'pcs', 1100], ['Core Web Vitals optimisation', 24, 'h', 50], ['Analytics setup', 1, 'pcs', 400]],
        'Customer portal' => [['Portal UX & design', 6, 'day', 380], ['Portal development', 180, 'h', 46], ['SSO integration', 1, 'pcs', 1500]],
        'Fleet tracking dashboard' => [['GPS data ingestion', 60, 'h', 50], ['Dashboard development', 100, 'h', 46], ['Driver mobile view', 40, 'h', 46]],
        'Cloud migration' => [['Infrastructure assessment', 3, 'day', 520], ['Migration & cut-over', 80, 'h', 58], ['Cloud cost optimisation', 1, 'pcs', 950]],
        'Inventory management system' => [['Process mapping', 24, 'h', 55], ['Inventory app development', 150, 'h', 46], ['Barcode scanner support', 1, 'pcs', 1300], ['User training', 1, 'day', 450]],
        'Staff training workshop' => [['Workshop preparation', 8, 'h', 50], ['On-site workshop', 2, 'day', 650], ['Training materials', 1, 'pcs', 300]],
    ];

    private const LOST_REASONS = ['Budget cut for this year', 'Chose a cheaper competitor', 'Project postponed indefinitely', 'No response after the proposal'];

    private const NOTES = [
        'Kick-off call went well — they want a phased rollout.',
        'Asked for references from similar projects in their industry.',
        'Decision maker is the CFO, needs ROI numbers before approving.',
        'They currently use spreadsheets and a legacy tool from 2014.',
        'Budget is approved for Q1, timeline is flexible.',
        'Sent the case study and a short video demo.',
        'Prefers communication by e-mail, available mornings.',
        'Competitor offer is ~15 % cheaper but without maintenance.',
    ];

    private const TASKS = [
        'Send follow-up e-mail with the proposal',
        'Prepare demo for the management meeting',
        'Call to confirm the budget',
        'Schedule a technical workshop',
        'Check contract draft with legal',
        'Send revised offer after negotiation',
        'Ask for a testimonial',
        'Book on-site visit',
    ];

    /** @var Collection<int, User> */
    private Collection $users;

    private array $audit = [];

    public function run(OfferService $offers): void
    {
        mt_srand(2026);
        Carbon::setTestNow(); // seed relative to "today"

        AuditLog::$recording = false;

        try {
            $this->seedUsers();
            $companies = $this->seedCompanies();
            $this->seedDeals($companies, $offers);
        } finally {
            AuditLog::$recording = true;
        }

        foreach (array_chunk($this->audit, 200) as $chunk) {
            AuditLog::insert($chunk);
        }
    }

    private function seedUsers(): void
    {
        $password = config('crm.demo.password');
        $domain = config('crm.demo.domain');

        $this->users = collect([
            ['Damir Peer', "admin@{$domain}", UserRole::Admin],
            ['Ana Kovač', "ana@{$domain}", UserRole::Sales],
            ['Marko Horvat', "marko@{$domain}", UserRole::Sales],
            ['Ivana Babić', "ivana@{$domain}", UserRole::Sales],
        ])->map(function (array $u) use ($password) {
            $user = User::create(['name' => $u[0], 'email' => $u[1], 'password' => $password, 'role' => $u[2]]);
            $user->forceFill(['email_verified_at' => now(), 'created_at' => now()->subMonths(8)])->saveQuietly();

            return $user;
        });
    }

    /** @return Collection<int, Company> */
    private function seedCompanies(): Collection
    {
        return collect(self::COMPANIES)->map(function (array $row, int $i) {
            [$name, $city, $industry] = $row;
            $slug = Str::of(Str::ascii($name))->before(' d.')->slug('')->lower();
            $owner = $this->pick($this->users);
            $created = now()->subDays(mt_rand(60, 240))->setTime(mt_rand(8, 17), mt_rand(0, 59));

            $company = Company::create([
                'name' => $name,
                'status' => CompanyStatus::Lead, // corrected below, after deals exist
                'oib' => $this->oib(),
                'industry' => $industry,
                'email' => "info@{$slug}.hr",
                'phone' => '+385 '.mt_rand(1, 99).' '.mt_rand(100, 999).' '.mt_rand(1000, 9999),
                'website' => "https://www.{$slug}.hr",
                'address' => $this->pick(['Ulica grada Vukovara', 'Savska cesta', 'Riva', 'Trg bana Jelačića', 'Industrijska zona', 'Obala kneza Branimira']).' '.mt_rand(1, 120),
                'city' => $city,
                'owner_id' => $owner->id,
            ]);
            $company->forceFill(['created_at' => $created, 'updated_at' => $created])->saveQuietly();
            $this->logCreated($company, $owner, $created);

            foreach (range(1, mt_rand(1, 3)) as $n) {
                $first = $this->pick(self::FIRST_NAMES);
                $last = $this->pick(self::LAST_NAMES);

                $contact = $company->contacts()->create([
                    'first_name' => $first,
                    'last_name' => $last,
                    'status' => $this->pick(ContactStatus::cases()),
                    'position' => $this->pick(self::POSITIONS),
                    'email' => Str::lower(Str::ascii("{$first}.{$last}"))."@{$slug}.hr",
                    'phone' => '+385 9'.mt_rand(1, 9).' '.mt_rand(100, 999).' '.mt_rand(1000, 9999),
                ]);
                $contact->forceFill(['created_at' => $created->copy()->addDays($n), 'updated_at' => $created])->saveQuietly();
            }

            return $company->load('contacts');
        });
    }

    private function seedDeals(Collection $companies, OfferService $offers): void
    {
        // stage => how many deals
        $plan = [
            DealStage::Lead->value => 6,
            DealStage::Qualified->value => 6,
            DealStage::Proposal->value => 6,
            DealStage::Negotiation->value => 5,
            DealStage::Won->value => 13,
            DealStage::Lost->value => 5,
        ];

        $specs = [];
        foreach ($plan as $stage => $count) {
            foreach (range(1, $count) as $i) {
                $specs[] = DealStage::from($stage);
            }
        }

        // Create in chronological order so offer numbers grow with dates.
        $specs = collect($specs)->map(function (DealStage $stage) {
            $created = match ($stage) {
                DealStage::Won, DealStage::Lost => now()->subDays(mt_rand(40, 200)),
                default => now()->subDays(mt_rand(3, 45)),
            };

            return ['stage' => $stage, 'created' => $created->setTime(mt_rand(8, 17), mt_rand(0, 59))];
        })->sortBy('created')->values();

        foreach ($specs as $spec) {
            /** @var DealStage $stage */
            $stage = $spec['stage'];
            /** @var Carbon $created */
            $created = $spec['created'];

            $company = $this->pick($companies);
            $owner = $company->owner_id ? $this->users->firstWhere('id', $company->owner_id) : $this->pick($this->users);
            $project = $this->pick(array_keys(self::PROJECTS));
            $items = $this->itemsFor($project);
            $value = round(collect($items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']), -1);

            $closed = $stage->isClosed() ? $created->copy()->addDays(mt_rand(10, 35))->min(now()->subDay()) : null;

            $deal = Deal::create([
                'company_id' => $company->id,
                'contact_id' => $company->contacts->isNotEmpty() ? $this->pick($company->contacts)->id : null,
                'owner_id' => $owner->id,
                'title' => $project.' — '.Str::before($company->name, ' d.'),
                'description' => $this->pick(self::NOTES),
                'value' => $value,
                'stage' => $stage,
                'expected_close_date' => $stage->isClosed() ? $closed : now()->addDays(mt_rand(-5, 60)),
                'lost_reason' => $stage === DealStage::Lost ? $this->pick(self::LOST_REASONS) : null,
            ]);
            $deal->forceFill([
                'created_at' => $created,
                'updated_at' => $closed ?? $created,
                'stage_changed_at' => $closed ?? $created->copy()->addDays(mt_rand(0, 3)),
                'closed_at' => $closed,
            ])->saveQuietly();

            $this->logCreated($deal, $owner, $created);
            if ($stage !== DealStage::Lead) {
                $this->logStage($deal, $owner, DealStage::Lead, $stage, $closed ?? $created->copy()->addDays(2));
            }

            $this->seedOffersFor($deal, $owner, $items, $offers, $created, $closed);
            $this->seedActivitiesFor($deal, $owner, $created, $closed);
        }

        // Companies with won deals are customers, the rest are leads/prospects/inactive.
        $companies->each(function (Company $company) {
            $stages = $company->deals()->pluck('stage');
            $status = match (true) {
                $stages->contains(DealStage::Won) => CompanyStatus::Customer,
                $stages->contains(fn ($s) => in_array($s, [DealStage::Proposal, DealStage::Negotiation], true)) => CompanyStatus::Prospect,
                $stages->isEmpty() && mt_rand(0, 2) === 0 => CompanyStatus::Inactive,
                default => CompanyStatus::Lead,
            };
            $company->forceFill(['status' => $status])->saveQuietly();
        });
    }

    private function seedOffersFor(Deal $deal, User $owner, array $items, OfferService $offers, Carbon $created, ?Carbon $closed): void
    {
        $status = match ($deal->stage) {
            DealStage::Proposal, DealStage::Negotiation => OfferStatus::Sent,
            DealStage::Won => OfferStatus::Accepted,
            DealStage::Lost => OfferStatus::Rejected,
            DealStage::Qualified => mt_rand(0, 1) ? OfferStatus::Draft : null,
            default => null,
        };

        if ($status === null) {
            return;
        }

        $issued = $created->copy()->addDays(mt_rand(2, 8))->min(now());
        $stage = $deal->stage;

        $offer = $offers->create($deal, $owner, [
            'issued_at' => $issued->toDateString(),
            'valid_until' => $issued->copy()->addDays(30)->toDateString(),
            'notes' => 'Payment: 40 % on order, 60 % on delivery. Prices exclude VAT.',
        ], $items);

        // OfferService advances the deal; restore the planned stage quietly.
        $deal->forceFill(['stage' => $stage])->saveQuietly();

        // Some negotiations needed a second, discounted revision.
        if ($deal->stage === DealStage::Negotiation && mt_rand(0, 1)) {
            $offer->forceFill(['status' => OfferStatus::Rejected])->saveQuietly();
            $revision = $offers->createRevision($offer->load('items'), $owner);
            $discounted = $revision->items->map(fn ($i) => ['discount_percent' => 10] + $i->only(['description', 'quantity', 'unit', 'unit_price']))->all();
            $offers->syncItems($revision, $discounted, quiet: true);
            $offer = $revision;
            $issued = $issued->copy()->addDays(5)->min(now());
        }

        // Open offers are still valid, except an occasional overdue one for the
        // nightly offers:expire command to pick up.
        $validUntil = match (true) {
            $status->isFinal() => $issued->copy()->addDays(30),
            $deal->stage === DealStage::Proposal && mt_rand(0, 3) === 0 => now()->subDays(2),
            default => $issued->copy()->addDays(30)->max(now()->addDays(mt_rand(5, 20))),
        };

        $offer->forceFill([
            'status' => $status,
            'issued_at' => $issued,
            'valid_until' => $validUntil,
            'sent_at' => $status === OfferStatus::Draft ? null : $issued,
            'created_at' => $issued,
            'updated_at' => $closed ?? $issued,
        ])->saveQuietly();

        $this->logCreated($offer, $owner, $issued);
        if ($status !== OfferStatus::Draft) {
            $this->audit[] = $this->auditRow($offer, $owner, 'updated', ['status' => ['old' => 'draft', 'new' => 'sent']], $issued->copy()->addHour()->min(now()));
        }
        if ($status->isFinal()) {
            $this->audit[] = $this->auditRow($offer, $owner, 'updated', ['status' => ['old' => 'sent', 'new' => $status->value]], $closed ?? $issued);
        }
    }

    private function seedActivitiesFor(Deal $deal, User $owner, Carbon $created, ?Carbon $closed): void
    {
        $end = $closed ?? now();

        foreach (range(1, mt_rand(1, 3)) as $i) {
            $at = Carbon::createFromTimestamp(mt_rand($created->getTimestamp(), $end->getTimestamp()));
            $type = $this->pick([ActivityType::Note, ActivityType::Call, ActivityType::Meeting, ActivityType::Email]);

            $activity = new Activity(['type' => $type, 'body' => $this->pick(self::NOTES), 'user_id' => $owner->id]);
            $deal->activities()->save($activity);
            $activity->forceFill(['created_at' => $at, 'updated_at' => $at])->saveQuietly();
        }

        // Open deals get a next step; some are overdue to make the dashboard realistic.
        if ($deal->isOpen()) {
            $due = now()->addDays(mt_rand(-4, 10))->setTime(mt_rand(9, 16), 0);
            $task = new Activity([
                'type' => ActivityType::Task,
                'title' => $this->pick(self::TASKS),
                'due_at' => $due,
                'assigned_to' => $owner->id,
                'user_id' => $owner->id,
                'completed_at' => mt_rand(0, 4) === 0 ? now()->subDay() : null,
            ]);
            $deal->activities()->save($task);
        }
    }

    /** @return list<array<string, mixed>> */
    private function itemsFor(string $project): array
    {
        return array_map(fn (array $i) => [
            'description' => $i[0],
            'quantity' => $i[1],
            'unit' => $i[2],
            'unit_price' => $i[3],
            'discount_percent' => 0,
        ], self::PROJECTS[$project]);
    }

    private function logCreated(Company|Deal|Offer|Contact $model, User $user, Carbon $at): void
    {
        $this->audit[] = $this->auditRow($model, $user, 'created', null, $at);
    }

    private function logStage(Deal $deal, User $user, DealStage $from, DealStage $to, Carbon $at): void
    {
        $this->audit[] = $this->auditRow($deal, $user, 'updated', ['stage' => ['old' => $from->value, 'new' => $to->value]], $at);
    }

    private function auditRow($model, User $user, string $event, ?array $changes, Carbon $at): array
    {
        return [
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'user_id' => $user->id,
            'event' => $event,
            'diff' => $changes ? json_encode($changes) : null,
            'created_at' => $at,
        ];
    }

    private function oib(): string
    {
        $first10 = (string) mt_rand(1000000000, 9999999999);

        return $first10.Oib::checkDigit($first10);
    }

    /**
     * @template T
     *
     * @param  iterable<T>  $items
     * @return T
     */
    private function pick(iterable $items): mixed
    {
        $items = $items instanceof Collection ? $items->values()->all() : array_values((array) $items);

        return $items[mt_rand(0, count($items) - 1)];
    }
}
