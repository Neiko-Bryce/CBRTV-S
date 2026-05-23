# Cloud-Based Real-Time Voting System (CBRTV-S) — Entity Relationship Diagram

This document describes the database schema as derived from the Laravel migrations in
`database/migrations/` and the Eloquent models in `app/Models/`.

> Note: All tables also include the standard Laravel `created_at` and `updated_at`
> timestamps. The `users` table additionally has `remember_token` and
> `email_verified_at` columns. These are omitted from the entity boxes below to keep
> the diagram readable.

---

## High-level relationships

```mermaid
erDiagram
    schools ||--o{ organizations : has
    schools ||--o{ users : has
    schools ||--o{ students : has
    schools ||--o{ elections : has
    organizations ||--o{ positions : has
    organizations ||--o{ elections : runs
    organizations ||--o{ users : has
    organizations ||--o{ students : has
    elections ||--o{ partylists : has
    elections ||--o{ candidates : has
    elections ||--o{ votes : has
    positions ||--o{ candidates : has
    partylists ||--o{ candidates : has
    students ||--o{ candidates : becomes
    candidates ||--o{ votes : receives
    users ||--o{ votes : casts
    users ||--o{ password_regeneration_history : has
    archived_elections ||--o{ archived_partylists : has
    archived_elections ||--o{ archived_candidates : has
    archived_elections ||--o{ archived_votes : has
    archived_candidates ||--o{ archived_votes : receives
```

---

## Detailed schema (all tables)

```mermaid
erDiagram
    schools {
        bigint id PK
        varchar name
        varchar slug UK
        varchar location "nullable"
        boolean is_active
        boolean maintenance_mode
    }

    users {
        bigint id PK
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar name
        varchar email "unique per organization"
        varchar password
        varchar usertype "admin or student"
        boolean is_super_admin
        int password_regenerated_count
    }

    students {
        bigint id PK
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar student_id_number "unique per org and school"
        varchar campus
        varchar fname "nullable"
        varchar lname
        varchar mname "nullable"
        varchar ext "nullable"
        varchar gender "nullable"
        varchar course "nullable"
        varchar yearlevel "nullable"
        varchar section "nullable"
    }

    organizations {
        bigint id PK
        bigint school_id FK "nullable"
        varchar name
        varchar slug "nullable, unique per school"
        varchar code "nullable, unique per school"
        text description "nullable"
        varchar logo_path "nullable"
        boolean is_active
    }

    positions {
        bigint id PK
        bigint organization_id FK
        bigint school_id FK "nullable"
        varchar name
        text description "nullable"
        int number_of_slots
        int order_index "column: order"
        boolean is_active
    }

    partylists {
        bigint id PK
        bigint election_id FK
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar name
        varchar code "nullable"
        text description "nullable"
        varchar color "nullable"
        varchar logo "nullable"
        boolean is_active
    }

    elections {
        bigint id PK
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar election_id "unique per school"
        varchar election_name
        varchar type_of_election
        text description "nullable"
        varchar venue "nullable"
        date election_date
        time timestarted "nullable"
        time time_ended "nullable"
        varchar status "default upcoming"
        boolean show_live_results
        int voter_capacity "nullable"
        varchar course_filter_mode "default all"
        json allowed_courses "nullable"
    }

    candidates {
        bigint id PK
        bigint election_id FK
        bigint position_id FK
        bigint partylist_id FK "nullable"
        bigint student_id FK "nullable"
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar candidate_name
        varchar photo "nullable"
        text biography "nullable"
        text platform "nullable"
        int votes_count
        boolean is_active
    }

    votes {
        bigint id PK
        bigint election_id FK
        bigint candidate_id FK
        bigint voter_id FK "users.id"
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
    }

    password_regeneration_history {
        bigint id PK
        bigint user_id FK
        varchar student_id
        timestamp regenerated_at
        varchar regenerated_by "nullable"
    }

    landing_page_settings {
        bigint id PK
        bigint organization_id FK "nullable"
        bigint school_id FK "nullable"
        varchar section "unique with key"
        varchar key_name "column: key, unique with section"
        text value "nullable"
        varchar image "nullable"
        json extra "nullable"
    }

    settings {
        bigint id PK
        varchar key_name UK "column: key"
        text value "nullable"
        varchar description "nullable"
    }

    archived_elections {
        bigint id PK
        bigint original_election_id UK
        bigint school_id FK "nullable"
        bigint organization_id FK "nullable"
        bigint archived_by FK "users.id, nullable"
        varchar election_id "nullable"
        varchar election_name
        varchar type_of_election "nullable"
        text description "nullable"
        varchar venue "nullable"
        date election_date "nullable"
        time timestarted "nullable"
        time time_ended "nullable"
        varchar status "default completed"
        boolean show_live_results
        timestamp archived_at
    }

    archived_partylists {
        bigint id PK
        bigint original_partylist_id "nullable"
        bigint archived_election_id FK
        bigint school_id FK "nullable"
        bigint organization_id FK "nullable"
        varchar name
        varchar code "nullable"
        text description "nullable"
        varchar color "nullable"
        varchar logo "nullable"
        boolean is_active
    }

    archived_candidates {
        bigint id PK
        bigint original_candidate_id "nullable"
        bigint archived_election_id FK
        bigint archived_partylist_id FK "nullable"
        bigint original_position_id "nullable"
        bigint student_id FK "nullable"
        bigint school_id FK "nullable"
        bigint organization_id FK "nullable"
        varchar position_name "nullable"
        int position_order
        int number_of_slots
        varchar candidate_name
        varchar photo "nullable"
        text biography "nullable"
        text platform "nullable"
        int votes_count
        boolean is_active
    }

    archived_votes {
        bigint id PK
        bigint original_vote_id "nullable"
        bigint archived_election_id FK
        bigint archived_candidate_id FK
        bigint voter_id FK "users.id, nullable"
        bigint school_id FK "nullable"
        bigint organization_id FK "nullable"
        timestamp voted_at "nullable"
    }

    schools ||--o{ organizations : has
    schools ||--o{ users : has
    schools ||--o{ students : has
    schools ||--o{ elections : has
    schools ||--o{ positions : has
    schools ||--o{ partylists : has
    schools ||--o{ candidates : has
    schools ||--o{ votes : has
    schools ||--o{ landing_page_settings : has
    schools ||--o{ archived_elections : has
    schools ||--o{ archived_partylists : has
    schools ||--o{ archived_candidates : has
    schools ||--o{ archived_votes : has
    organizations ||--o{ users : has
    organizations ||--o{ students : has
    organizations ||--o{ positions : has
    organizations ||--o{ partylists : has
    organizations ||--o{ elections : runs
    organizations ||--o{ candidates : has
    organizations ||--o{ votes : has
    organizations ||--o{ landing_page_settings : has
    organizations ||--o{ archived_elections : has
    organizations ||--o{ archived_partylists : has
    organizations ||--o{ archived_candidates : has
    organizations ||--o{ archived_votes : has
    elections ||--o{ partylists : has
    elections ||--o{ candidates : has
    elections ||--o{ votes : has
    positions ||--o{ candidates : has
    partylists ||--o{ candidates : has
    students ||--o{ candidates : becomes
    students ||--o{ archived_candidates : becomes
    candidates ||--o{ votes : receives
    users ||--o{ votes : casts
    users ||--o{ password_regeneration_history : has
    users ||--o{ archived_elections : archived_by
    users ||--o{ archived_votes : casts
    archived_elections ||--o{ archived_partylists : has
    archived_elections ||--o{ archived_candidates : has
    archived_elections ||--o{ archived_votes : has
    archived_partylists ||--o{ archived_candidates : has
    archived_candidates ||--o{ archived_votes : receives
```

> Mermaid does not allow the reserved words `key` and `order` as column names, so the
> diagram renames `landing_page_settings.key` to `key_name`, `settings.key` to
> `key_name`, and `positions.order` to `order_index`. The actual database column
> names are `key` and `order` respectively (see `schema.dbml` for the exact column
> names).

---

## Composite unique constraints

| Table                    | Unique columns                                              |
|--------------------------|-------------------------------------------------------------|
| users                    | (`organization_id`, `email`)                                |
| students                 | (`organization_id`, `student_id_number`) and (`student_id_number`, `school_id`) |
| organizations            | (`slug`, `school_id`) and (`code`, `school_id`)             |
| elections                | (`election_id`, `school_id`)                                |
| votes                    | (`election_id`, `voter_id`, `candidate_id`)                 |
| landing_page_settings    | (`section`, `key`)                                          |

## Single-column unique constraints

| Table                    | Unique column            |
|--------------------------|--------------------------|
| schools                  | `slug`                   |
| settings                 | `key`                    |
| archived_elections       | `original_election_id`   |

---

## Notes

- The system is multi-tenant. Most tables carry both `school_id` (the tenant root)
  and `organization_id` (a sub-grouping such as `SSG`, `FLP`, `Classroom`).
- A `user` with `usertype = 'admin'` can also be `is_super_admin = true`, in which
  case `school_id` is typically `NULL` (global admin).
- A `vote` belongs to a `user` (the voter, via `voter_id`) — students vote through
  their `users` account, not directly via the `students` row.
- The `archived_*` tables freeze a snapshot of an election at the moment it is
  archived. They reference live `students` and `users` (so historical references
  survive) but use `nullOnDelete()` so deleting a live row does not break the
  archive.
- The standalone `settings` table is a global key/value store. The legacy global
  `maintenance_mode` setting was migrated into a per-school `schools.maintenance_mode`
  column.
