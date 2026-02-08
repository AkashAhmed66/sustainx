
---

# 🧱 COMPLETE DATABASE SCHEMA (FINAL)

---

## 1️⃣ sections

```sql
id (PK)
name
description
order_no
is_active
created_at
updated_at
```

---

## 2️⃣ subsections

```sql
id (PK)
section_id (FK → sections.id)
name
description
order_no
is_active
created_at
updated_at
```

**Relation**

* Section → many Subsections

---

## 3️⃣ items

```sql
id (PK)
subsection_id (FK → subsections.id)
name
description
order_no
is_active
created_at
updated_at
```

**Relation**

* Subsection → many Items

---

## 4️⃣ question_types

```sql
id (PK)
name        -- numeric, text, boolean, mcq
created_at
updated_at
```

---

## 5️⃣ questions

```sql
id (PK)
item_id (FK → items.id)
question_text
question_type_id (FK → question_types.id)
unit
is_required
is_active
created_at
updated_at
```

✔ One question belongs to one item
✔ Only numeric questions will have equations

---

## 6️⃣ options (MCQ only)

```sql
id (PK)
question_id (FK → questions.id)
option_text
option_value     -- optional numeric score
order_no
created_at
updated_at
```

---

# 🌍 LOCATION & FACTORIES

---

## 7️⃣ countries

```sql
id (PK)
name
iso_code
created_at
updated_at
```

---

## 8️⃣ factory_types

```sql
id (PK)
name
created_at
updated_at
```

---

## 9️⃣ factories

```sql
id (PK)
name
factory_type_id (FK → factory_types.id)
country_id (FK → countries.id)
address
is_active
created_at
updated_at
```

---

## 🔟 factory_user (many-to-many)

```sql
id (PK)
factory_id (FK → factories.id)
user_id (FK → users.id)
role        -- admin, auditor, manager
created_at
updated_at
```

---

# 📋 ASSESSMENTS (VERY IMPORTANT)

---

## 1️⃣1️⃣ assessments

```sql
id (PK)
factory_id (FK → factories.id)
year
period        -- annual, quarterly
status        -- draft, submitted, approved
submitted_at
created_at
updated_at
```

✔ One factory
✔ Multiple ESG submissions
✔ Supports year-wise comparison

---

# ✍️ ANSWERS (USER INPUT)

---

## 1️⃣2️⃣ answers

```sql
id (PK)
assessment_id (FK → assessments.id)
question_id (FK → questions.id)
item_id (FK → items.id)

numeric_value   DECIMAL(15,4) NULL
text_value      TEXT NULL
option_id       (FK → options.id) NULL

created_at
updated_at
```

✔ Question → many answers
✔ Assessment → many answers
✔ Clean separation of answer types

---

# 🧮 SIMPLE & PROFESSIONAL EQUATION ENGINE

---

## 1️⃣3️⃣ equations

```sql
id (PK)
question_id (FK → questions.id)
name
created_at
updated_at
```

⚠️ **Important rule**

* Only **ONE equation per question**
* Only numeric questions have equations

---

## 1️⃣4️⃣ factors

```sql
id (PK)
equation_id (FK → equations.id)
sn               -- serial number (1, 2, 3…)
operation        -- multiply, add, subtract, divide
factor_value     -- numeric value
country_id (FK → countries.id) NULL
created_at
updated_at
```

✔ Factors are applied **in order of `sn`**
✔ Country-specific factors supported
✔ No reuse complexity (simple & safe)

---

# 📊 STORED RESULTS (FAST DASHBOARDS)

---

## 1️⃣5️⃣ calculation_results

```sql
id (PK)
assessment_id (FK → assessments.id)
question_id (FK → questions.id)
item_id (FK → items.id)
final_value
created_at
updated_at
```

✔ Result stored once
✔ Charts load instantly
✔ No recalculation on every page load

---
