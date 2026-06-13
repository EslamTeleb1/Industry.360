import json

INPUT = "postman_collection.json"
with open(INPUT, encoding="utf-8") as f:
    col = json.load(f)

for item in col.get("item", []):
    # Rename contact to home
    if "Contact" in item["name"] and "Us" not in item["name"]:
        item["name"] = item["name"].replace("Contact", "Home")
    
    # Update URLs
    if "request" in item and "url" in item["request"]:
        url = item["request"]["url"].get("raw", "")
        url = url.replace("contact-", "home-")
        url = url.replace("contact/", "home/")
        # for public contact POST
        if url.endswith("/api/contact"):
            url = url.replace("/api/contact", "/api/home")
        item["request"]["url"]["raw"] = url
        
    # Update Team Member payload with tag
    if item["name"] in ["Create Team Member (Admin)", "Update Team Member (Admin)"]:
        if "body" in item["request"] and "formdata" in item["request"]["body"]:
            fd = item["request"]["body"]["formdata"]
            if not any(f.get("key") == "tag" for f in fd):
                fd.append({"key": "tag", "value": "Founder", "type": "text"})
    
    # Add tag to Team Member example response
    for resp in item.get("response", []):
        if "Team Member" in item["name"]:
            try:
                body_str = resp.get("body", "")
                if body_str:
                    body = json.loads(body_str)
                    if isinstance(body, dict) and "data" in body and body["data"] is not None and "team_member" in body["data"]:
                        body["data"]["team_member"]["tag"] = "Founder"
                        resp["body"] = json.dumps(body, ensure_ascii=False)
            except json.JSONDecodeError:
                pass

# Add new endpoints
items = col["item"]

def insert_after(after_name, new_item):
    for i, it in enumerate(items):
        if it.get("name") == after_name:
            items.insert(i + 1, new_item)
            return
    items.append(new_item)

# 1. Admin Stats
if not any(it["name"] == "Get Admin Stats" for it in items):
    stats_item = {
        "name": "Get Admin Stats",
        "request": {
            "method": "GET",
            "header": [{"key": "Authorization", "value": "Bearer {{token}}"}],
            "url": {"raw": "{{base_url}}/api/admin/stats"}
        },
        "response": [{
            "name": "Example Response",
            "originalRequest": {
                "method": "GET",
                "url": {"raw": "{{base_url}}/api/admin/stats"}
            },
            "status": "OK",
            "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps({"success":True,"message":"Admin stats retrieved successfully","data":{"stats":{"services_count":10,"solutions_count":5,"industries_count":4,"careers_count":3,"job_applications_count":12,"home_messages_count":20,"team_members_count":8,"blogs_count":15,"case_studies_count":6,"partners_count":10,"faqs_count":25,"packages_count":7,"active_services_count":8,"active_solutions_count":4,"active_careers_count":2,"recent_home_messages":5,"recent_job_applications":3}},"meta":{},"timestamp":"2026-06-12T10:00:00+00:00"}, ensure_ascii=False)
        }]
    }
    insert_after("Admin Me", stats_item)

# 2. Contact Us Setting (Public)
if not any(it["name"] == "Get Contact Us Setting (Public)" for it in items):
    setting_public = {
        "name": "Get Contact Us Setting (Public)",
        "request": {
            "method": "GET",
            "header": [],
            "url": {"raw": "{{base_url}}/api/contact-us/setting"}
        },
        "response": [{
            "name": "Example Response",
            "originalRequest": {"method": "GET", "url": {"raw": "{{base_url}}/api/contact-us/setting"}},
            "status": "OK", "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps({"success":True,"message":"Contact Us setting retrieved successfully","data":{"setting":{"id":1,"title":"Contact Us","title_en":"Contact Us","title_ar":"اتصل بنا","description":"Get in touch","description_en":"Get in touch","description_ar":"تواصل معنا"}},"meta":{},"timestamp":"2026-06-12T10:00:00+00:00"}, ensure_ascii=False)
        }]
    }
    insert_after("Get About Us Setting (Public)", setting_public)

# 3. Contact Us Setting (Admin)
if not any(it["name"] == "Get Contact Us Setting (Admin)" for it in items):
    items.append({
        "name": "Get Contact Us Setting (Admin)",
        "request": {
            "method": "GET",
            "header": [{"key": "Authorization", "value": "Bearer {{token}}"}],
            "url": {"raw": "{{base_url}}/api/admin/contact-us/setting"}
        },
        "response": [{
            "name": "Example Response",
            "originalRequest": {"method": "GET", "url": {"raw": "{{base_url}}/api/admin/contact-us/setting"}},
            "status": "OK", "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps({"success":True,"message":"Contact Us setting retrieved successfully","data":{"setting":{"id":1,"title":"Contact Us","title_en":"Contact Us","title_ar":"اتصل بنا","description":"Get in touch","description_en":"Get in touch","description_ar":"تواصل معنا"}},"meta":{},"timestamp":"2026-06-12T10:00:00+00:00"}, ensure_ascii=False)
        }]
    })

if not any(it["name"] == "Update Contact Us Setting (Admin)" for it in items):
    items.append({
        "name": "Update Contact Us Setting (Admin)",
        "request": {
            "method": "POST",
            "header": [{"key": "Authorization", "value": "Bearer {{token}}"}, {"key": "Content-Type", "value": "application/json"}],
            "body": {
                "mode": "raw",
                "raw": json.dumps({"title":{"en":"Contact Us","ar":"اتصل بنا"},"description":{"en":"Get in touch","ar":"تواصل معنا"}}, ensure_ascii=False, indent=2)
            },
            "url": {"raw": "{{base_url}}/api/admin/contact-us/setting"}
        },
        "response": [{
            "name": "Example Response",
            "originalRequest": {"method": "POST", "url": {"raw": "{{base_url}}/api/admin/contact-us/setting"}},
            "status": "OK", "code": 200,
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": json.dumps({"success":True,"message":"Contact Us setting updated successfully","data":{"setting":{"id":1,"title":"Contact Us","title_en":"Contact Us","title_ar":"اتصل بنا","description":"Get in touch","description_en":"Get in touch","description_ar":"تواصل معنا"}},"meta":{},"timestamp":"2026-06-12T10:00:00+00:00"}, ensure_ascii=False)
        }]
    })

with open(INPUT, "w", encoding="utf-8") as f:
    json.dump(col, f, ensure_ascii=False, indent=4)

print("Postman collection updated successfully.")
