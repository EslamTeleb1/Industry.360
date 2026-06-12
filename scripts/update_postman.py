#!/usr/bin/env python3
"""
Updates the Postman collection to reflect:
1. Team Member: replace `link` with `description` + `social_links`, add public show endpoint
2. Vision Message: remove img/percentage/is_active fields from create/update
3. About Us: add all new admin + public endpoints (with examples)
"""

import json, copy, sys

INPUT  = "postman_collection.json"
OUTPUT = "postman_collection.json"

with open(INPUT, "r", encoding="utf-8") as f:
    collection = json.load(f)

items = collection["item"]

# ── helpers ─────────────────────────────────────────────────────────────────

def auth_header():
    return [{"key": "Authorization", "value": "Bearer {{token}}"}]

def make_get(name, url_raw, *, auth=True, variables=None, query=None, example_body=None):
    url = {"raw": url_raw}
    if variables:
        url["variable"] = variables
    if query:
        url["query"] = query
    req = {
        "name": name,
        "request": {
            "method": "GET",
            "header": auth_header() if auth else [],
            "url": url,
        },
        "response": [],
    }
    if example_body:
        req["response"] = [{
            "name": f"Example Response",
            "originalRequest": {"method": "GET", "url": url},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(example_body),
        }]
    return req

def make_post_json(name, url_raw, body_dict, *, auth=True, variables=None, example_body=None):
    url = {"raw": url_raw}
    if variables:
        url["variable"] = variables
    req = {
        "name": name,
        "request": {
            "method": "POST",
            "header": (auth_header() + [{"key": "Content-Type", "value": "application/json"}]) if auth else [{"key": "Content-Type", "value": "application/json"}],
            "body": {"mode": "raw", "raw": json.dumps(body_dict, ensure_ascii=False, indent=2)},
            "url": url,
        },
        "response": [],
    }
    if example_body:
        req["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": url},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(example_body),
        }]
    return req

def make_post_formdata(name, url_raw, formdata, *, auth=True, variables=None, example_body=None):
    url = {"raw": url_raw}
    if variables:
        url["variable"] = variables
    req = {
        "name": name,
        "request": {
            "method": "POST",
            "header": auth_header() if auth else [],
            "body": {"mode": "formdata", "formdata": formdata},
            "url": url,
        },
        "response": [],
    }
    if example_body:
        req["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": url},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(example_body),
        }]
    return req

def make_delete(name, url_raw, variables=None):
    url = {"raw": url_raw}
    if variables:
        url["variable"] = variables
    return {
        "name": name,
        "request": {
            "method": "DELETE",
            "header": auth_header(),
            "url": url,
        },
        "response": [],
    }

def text(k, v):
    return {"key": k, "value": v, "type": "text"}

def file_field(k):
    return {"key": k, "type": "file", "src": ""}

# ── example payloads ─────────────────────────────────────────────────────────

TEAM_MEMBER_EXAMPLE = {
    "success": True,
    "message": "Team member retrieved successfully",
    "data": {
        "team_member": {
            "id": 1,
            "title": "John Doe",
            "title_en": "John Doe",
            "title_ar": "جون دو",
            "position": "CEO",
            "position_en": "CEO",
            "position_ar": "الرئيس التنفيذي",
            "description": "An experienced leader with 15+ years in the tech industry.",
            "description_en": "An experienced leader with 15+ years in the tech industry.",
            "description_ar": "قائد متمرس بأكثر من 15 عامًا في مجال التقنية.",
            "social_links": [
                {"platform": "linkedin", "url": "https://linkedin.com/in/johndoe"},
                {"platform": "twitter", "url": "https://twitter.com/johndoe"},
            ],
            "img_path": "team_members/photo.jpg",
            "img_url": "http://localhost:8000/storage/team_members/photo.jpg",
            "is_active": True,
            "created_at": "2026-06-12 10:00:00",
            "updated_at": "2026-06-12 10:00:00",
        }
    },
    "meta": {},
    "timestamp": "2026-06-12T10:00:00+00:00",
}

VISION_MESSAGE_EXAMPLE = {
    "success": True,
    "message": "Vision message retrieved successfully",
    "data": {
        "vision_message": {
            "id": 1,
            "title": "Our Vision",
            "title_en": "Our Vision",
            "title_ar": "رؤيتنا",
            "description": "Leading digital transformation across the region.",
            "description_en": "Leading digital transformation across the region.",
            "description_ar": "قيادة التحول الرقمي في المنطقة.",
            "created_at": "2026-06-12 10:00:00",
            "updated_at": "2026-06-12 10:00:00",
        }
    },
    "meta": {},
    "timestamp": "2026-06-12T10:00:00+00:00",
}

ABOUT_US_EXAMPLE = {
    "success": True,
    "message": "About Us setting retrieved successfully",
    "data": {
        "setting": {
            "id": 1,
            "title": "About Industry360",
            "title_en": "About Industry360",
            "title_ar": "عن إندستري 360",
            "description": "We are a leading technology company.",
            "description_en": "We are a leading technology company.",
            "description_ar": "نحن شركة تقنية رائدة.",
            "sub_title": "What We Achieved",
            "sub_title_en": "What We Achieved",
            "sub_title_ar": "ما حققناه",
            "sub_description": "Our numbers speak for themselves.",
            "sub_description_en": "Our numbers speak for themselves.",
            "sub_description_ar": "أرقامنا تتحدث عن نفسها.",
            "percentage_title_1": "Customer Satisfaction",
            "percentage_title_1_en": "Customer Satisfaction",
            "percentage_title_1_ar": "رضا العملاء",
            "percentage_description_1": "Clients who rate us 5 stars",
            "percentage_description_1_en": "Clients who rate us 5 stars",
            "percentage_description_1_ar": "عملاء يمنحوننا 5 نجوم",
            "percentage_value_1": 95,
            "percentage_title_2": "Project Success",
            "percentage_title_2_en": "Project Success",
            "percentage_title_2_ar": "نجاح المشاريع",
            "percentage_description_2": "Projects delivered on time",
            "percentage_description_2_en": "Projects delivered on time",
            "percentage_description_2_ar": "مشاريع تُسلَّم في الوقت المحدد",
            "percentage_value_2": 88,
            "percentage_title_3": "Growth Rate",
            "percentage_title_3_en": "Growth Rate",
            "percentage_title_3_ar": "معدل النمو",
            "percentage_description_3": "Year-over-year revenue growth",
            "percentage_description_3_en": "Year-over-year revenue growth",
            "percentage_description_3_ar": "نمو الإيرادات على أساس سنوي",
            "percentage_value_3": 72,
            "created_at": "2026-06-12 10:00:00",
            "updated_at": "2026-06-12 10:00:00",
        }
    },
    "meta": {},
    "timestamp": "2026-06-12T10:00:00+00:00",
}

# ── 1. Add `teamMember` variable if missing ──────────────────────────────────
var_keys = [v["key"] for v in collection.get("variable", [])]
if "teamMember" not in var_keys:
    collection["variable"].append({"key": "teamMember", "value": "1"})

# ── 2. Update Vision Message - Create (remove percentage/img/is_active) ──────
for item in items:
    if item.get("name") == "Create Vision Message (Admin)":
        item["request"]["body"] = {
            "mode": "formdata",
            "formdata": [
                text("title[en]", "Our Vision"),
                text("title[ar]", "رؤيتنا"),
                text("description[en]", "Leading digital transformation across the region."),
                text("description[ar]", "قيادة التحول الرقمي في المنطقة."),
            ],
        }
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": {"raw": "{{base_url}}/api/admin/vision-messages"}},
            "status": "Created",
            "code": 201,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(VISION_MESSAGE_EXAMPLE),
        }]
        print("✓ Updated: Create Vision Message (Admin)")

# ── 3. Update Vision Message - Update (remove percentage/img) ────────────────
    if item.get("name") == "Update Vision Message (Admin)":
        item["request"]["body"] = {
            "mode": "formdata",
            "formdata": [
                text("title[en]", "Our Vision Updated"),
                text("title[ar]", "رؤيتنا محدثة"),
                text("description[en]", "Driving innovation forward."),
                text("description[ar]", "دفع الابتكار إلى الأمام."),
            ],
        }
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": {"raw": "{{base_url}}/api/admin/vision-messages/:visionMessage", "variable": [{"key": "visionMessage", "value": "{{visionMessage}}"}]}},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(VISION_MESSAGE_EXAMPLE),
        }]
        print("✓ Updated: Update Vision Message (Admin)")

# ── 4. Update Team Member - Create (link → description + social_links) ───────
    if item.get("name") == "Create Team Member (Admin)":
        item["request"]["body"] = {
            "mode": "formdata",
            "formdata": [
                text("title[en]", "John Doe"),
                text("title[ar]", "جون دو"),
                text("position[en]", "CEO"),
                text("position[ar]", "الرئيس التنفيذي"),
                text("description[en]", "An experienced leader with 15+ years in the tech industry."),
                text("description[ar]", "قائد متمرس بأكثر من 15 عامًا في مجال التقنية."),
                text("social_links[0][platform]", "linkedin"),
                text("social_links[0][url]", "https://linkedin.com/in/johndoe"),
                text("social_links[1][platform]", "twitter"),
                text("social_links[1][url]", "https://twitter.com/johndoe"),
                file_field("img"),
                text("is_active", "1"),
            ],
        }
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": {"raw": "{{base_url}}/api/admin/team-members"}},
            "status": "Created",
            "code": 201,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(TEAM_MEMBER_EXAMPLE),
        }]
        print("✓ Updated: Create Team Member (Admin)")

# ── 5. Update Team Member - Update (link → description + social_links) ───────
    if item.get("name") == "Update Team Member (Admin)":
        item["request"]["body"] = {
            "mode": "formdata",
            "formdata": [
                text("title[en]", "John Doe Updated"),
                text("title[ar]", "جون دو محدث"),
                text("position[en]", "CTO"),
                text("position[ar]", "المدير التقني"),
                text("description[en]", "Now leading our engineering division."),
                text("description[ar]", "يقود الآن قسم الهندسة لدينا."),
                text("social_links[0][platform]", "linkedin"),
                text("social_links[0][url]", "https://linkedin.com/in/johndoe-updated"),
                file_field("img"),
            ],
        }
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": {"raw": "{{base_url}}/api/admin/team-members/:teamMember", "variable": [{"key": "teamMember", "value": "{{teamMember}}"}]}},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(TEAM_MEMBER_EXAMPLE),
        }]
        print("✓ Updated: Update Team Member (Admin)")

# ── 6. Add example to Get Team Member (Admin) if missing ────────────────────
    if item.get("name") == "Get Team Member (Admin)" and not item.get("response"):
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "GET", "url": {"raw": "{{base_url}}/api/admin/team-members/:teamMember", "variable": [{"key": "teamMember", "value": "{{teamMember}}"}]}},
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(TEAM_MEMBER_EXAMPLE),
        }]
        print("✓ Updated: Get Team Member (Admin) - added example")

    if item.get("name") == "Get Vision Message (Admin)" and not item.get("response"):
        item["response"] = [{
            "name": "Example Response",
            "originalRequest": {"method": "GET", "url": {"raw": "{{base_url}}/api/admin/vision-messages/:visionMessage"}},
            "status": "OK", "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps(VISION_MESSAGE_EXAMPLE),
        }]
        print("✓ Updated: Get Vision Message (Admin) - added example")

# ── 7. Find insertion index - after "List Team Members (Public)" ─────────────
public_team_idx = next((i for i, it in enumerate(items) if it.get("name") == "List Team Members (Public)"), None)

if public_team_idx is not None:
    # Check if show endpoint already exists
    show_exists = any(it.get("name") == "Get Team Member (Public)" for it in items)
    if not show_exists:
        show_item = make_get(
            "Get Team Member (Public)",
            "{{base_url}}/api/team-members/:teamMember",
            auth=False,
            variables=[{"key": "teamMember", "value": "{{teamMember}}"}],
            example_body=TEAM_MEMBER_EXAMPLE,
        )
        items.insert(public_team_idx + 1, show_item)
        print("✓ Inserted: Get Team Member (Public)")

# ── 8. Add About Us public endpoint (after Show Team Member public) ──────────
public_about_exists = any(it.get("name") == "Get About Us Setting (Public)" for it in items)
if not public_about_exists:
    # Insert after show team member public
    show_team_idx = next((i for i, it in enumerate(items) if it.get("name") == "Get Team Member (Public)"), public_team_idx + 1)
    about_public = make_get(
        "Get About Us Setting (Public)",
        "{{base_url}}/api/about-us/setting",
        auth=False,
        example_body=ABOUT_US_EXAMPLE,
    )
    items.insert(show_team_idx + 1, about_public)
    print("✓ Inserted: Get About Us Setting (Public)")

# ── 9. Add About Us admin endpoints (at end, before closing) ─────────────────
admin_about_exists = any(it.get("name") == "Get About Us Setting (Admin)" for it in items)
if not admin_about_exists:
    about_admin_get = make_get(
        "Get About Us Setting (Admin)",
        "{{base_url}}/api/admin/about-us/setting",
        auth=True,
        example_body=ABOUT_US_EXAMPLE,
    )
    about_admin_update = make_post_json(
        "Update About Us Setting (Admin)",
        "{{base_url}}/api/admin/about-us/setting",
        {
            "title": {"en": "About Industry360", "ar": "عن إندستري 360"},
            "description": {"en": "We are a leading technology company.", "ar": "نحن شركة تقنية رائدة."},
            "sub_title": {"en": "What We Achieved", "ar": "ما حققناه"},
            "sub_description": {"en": "Our numbers speak for themselves.", "ar": "أرقامنا تتحدث عن نفسها."},
            "percentage_title_1": {"en": "Customer Satisfaction", "ar": "رضا العملاء"},
            "percentage_description_1": {"en": "Clients who rate us 5 stars", "ar": "عملاء يمنحوننا 5 نجوم"},
            "percentage_value_1": 95,
            "percentage_title_2": {"en": "Project Success", "ar": "نجاح المشاريع"},
            "percentage_description_2": {"en": "Projects delivered on time", "ar": "مشاريع تُسلَّم في الوقت المحدد"},
            "percentage_value_2": 88,
            "percentage_title_3": {"en": "Growth Rate", "ar": "معدل النمو"},
            "percentage_description_3": {"en": "Year-over-year revenue growth", "ar": "نمو الإيرادات على أساس سنوي"},
            "percentage_value_3": 72,
        },
        auth=True,
        example_body=ABOUT_US_EXAMPLE,
    )
    items.append(about_admin_get)
    items.append(about_admin_update)
    print("✓ Inserted: Get About Us Setting (Admin)")
    print("✓ Inserted: Update About Us Setting (Admin)")

# ── save ─────────────────────────────────────────────────────────────────────
with open(OUTPUT, "w", encoding="utf-8") as f:
    json.dump(collection, f, ensure_ascii=False, indent=4)

print(f"\n✅ Done — saved to {OUTPUT}")
