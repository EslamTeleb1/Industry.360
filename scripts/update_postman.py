#!/usr/bin/env python3
"""Full Postman collection updater – adds body + response example to every endpoint."""
import json, sys, os
sys.path.insert(0, os.path.dirname(__file__))
from postman_data import *

INPUT = OUTPUT = "postman_collection.json"

with open(INPUT, encoding="utf-8") as f:
    col = json.load(f)

items = col["item"]

# ── helpers ──────────────────────────────────────────────────────────────────

def ex(method, url_raw, body_dict, resp_dict, status=200, code=200):
    """Build a saved example (request + response pair)."""
    orig = {"method": method, "url": {"raw": url_raw}}
    if body_dict and method != "GET":
        orig["body"] = body_dict
    return {
        "name": "Example Response",
        "originalRequest": orig,
        "status": "OK" if status == 200 else "Created",
        "code": code,
        "header": [{"key": "Content-Type", "value": "application/json"}],
        "body": json.dumps(resp_dict, ensure_ascii=False),
    }

def jbody(d):
    return {"mode": "raw", "raw": json.dumps(d, ensure_ascii=False, indent=2)}

def fbody(fields):
    return {"mode": "formdata", "formdata": [{"key": k, "value": v, "type": "text"} if v != "__file__" else {"key": k, "type": "file", "src": ""} for k, v in fields]}

def set_item(name, body=None, resp=None, code=200):
    for it in items:
        if it.get("name") == name:
            if body is not None:
                it["request"]["body"] = body
            if resp is not None:
                url_raw = it["request"]["url"].get("raw", "")
                method  = it["request"].get("method", "GET")
                it["response"] = [ex(method, url_raw, body, resp, code, code)]
            return True
    return False

def insert_after(after_name, new_item):
    for i, it in enumerate(items):
        if it.get("name") == after_name:
            items.insert(i + 1, new_item)
            return
    items.append(new_item)

def make_item(name, method, url_raw, body=None, resp=None, auth=True, variables=None, query=None, code=200):
    hdr = [{"key": "Authorization", "value": "Bearer {{token}}"}] if auth else []
    if body and method != "GET" and "formdata" not in str(body):
        hdr.append({"key": "Content-Type", "value": "application/json"})
    url = {"raw": url_raw}
    if variables: url["variable"] = variables
    if query:     url["query"]    = query
    req = {"method": method, "header": hdr, "url": url}
    if body: req["body"] = body
    response = [ex(method, url_raw, body, resp, code, code)] if resp else []
    return {"name": name, "request": req, "response": response}

# ══════════════════════════════════════════════════════════════════════════════
# AUTH
# ══════════════════════════════════════════════════════════════════════════════
set_item("Admin Me",         resp=ok("admin", ADMIN_ME, "Admin retrieved successfully"))
set_item("Get Roles",        resp=ok("roles", ["admin","editor","viewer"], "Roles retrieved"))
set_item("Get Permissions",  resp=ok("permissions", ["view-dashboard","view-users","edit-users"], "Permissions retrieved"))
set_item("Assign Roles to User",
    body=jbody({"roles":["admin","editor"]}),
    resp=ok("message","Roles assigned successfully"))
set_item("Assign Permissions to User",
    body=jbody({"permissions":["edit-users"]}),
    resp=ok("message","Permissions assigned successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# CAREERS – Departments
# ══════════════════════════════════════════════════════════════════════════════
set_item("Create Department (Admin)",
    resp=ok("department", DEPT, "Department created successfully"), code=201)
set_item("Update Department (Admin)",
    resp=ok("department", DEPT, "Department updated successfully"))
set_item("Delete Department (Admin)",
    resp=deleted("Department deleted successfully"))

# Locations
set_item("List Locations (Admin)",
    resp=list_ok("locations", [LOCATION], "Locations retrieved successfully"))
set_item("Create Location (Admin)",
    resp=ok("location", LOCATION, "Location created successfully"), code=201)
set_item("Update Location (Admin)",
    resp=ok("location", LOCATION, "Location updated successfully"))
set_item("Delete Location (Admin)",
    resp=deleted("Location deleted successfully"))

# Job Types
set_item("Create Job Type (Admin)",
    resp=ok("job_type", JOB_TYPE, "Job type created successfully"), code=201)
set_item("Update Job Type (Admin)",
    resp=ok("job_type", JOB_TYPE, "Job type updated successfully"))
set_item("Delete Job Type (Admin)",
    resp=deleted("Job type deleted successfully"))

# Jobs
set_item("Create Job (Admin)",
    resp=ok("job", JOB, "Job created successfully"), code=201)
set_item("Get Job (Admin)",
    resp=ok("job", JOB, "Job retrieved successfully"))
set_item("Update Job (Admin)",
    resp=ok("job", JOB, "Job updated successfully"))
set_item("Delete Job (Admin)",
    resp=deleted("Job deleted successfully"))
set_item("Get Job (Public)",
    resp=ok("job", JOB, "Job retrieved successfully"))

# Career Page
set_item("Get Admin Career Page",
    resp=ok("page", CAREER_PAGE, "Career page retrieved successfully"))
set_item("Update Admin Career Page (banner optional)",
    resp=ok("page", CAREER_PAGE, "Career page updated successfully"))
set_item("Public Career Page",
    resp=ok("page", CAREER_PAGE, "Career page retrieved successfully"))

# Applications
set_item("List Applications (Admin)",
    resp=list_ok("applications", [APPLICATION], "Applications retrieved successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# SERVICES / SOLUTIONS / INDUSTRIES
# ══════════════════════════════════════════════════════════════════════════════
set_item("Get Service (Public)",  resp=ok("service",  SERVICE,  "Service retrieved successfully"))
set_item("List Solutions (Public)",resp=list_ok("solutions",[SOLUTION],"Solutions retrieved successfully"))
set_item("Get Solution (Public)", resp=ok("solution", SOLUTION, "Solution retrieved successfully"))

set_item("List Services (Admin)", resp=list_ok("services",[SERVICE],"Services retrieved successfully"))
set_item("Create Service (Admin)",
    resp=ok("service",SERVICE,"Service created successfully"), code=201)
set_item("Get Service (Admin)",   resp=ok("service",  SERVICE,  "Service retrieved successfully"))
set_item("Update Service (Admin)",resp=ok("service",  SERVICE,  "Service updated successfully"))
set_item("Delete Service (Admin)",resp=deleted("Service deleted successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# BLOGS
# ══════════════════════════════════════════════════════════════════════════════
for nm, data, msg in [
    ("List Blog Categories (Public)",  list_ok("categories",[BLOG_CAT],"Categories retrieved"), None),
    ("List Blogs (Public)",            list_ok("blogs",[BLOG],"Blogs retrieved"),               None),
    ("Get Blog (Public)",              ok("blog",BLOG,"Blog retrieved"),                         None),
    ("List Blog Categories (Admin)",   list_ok("categories",[BLOG_CAT],"Categories retrieved"), None),
    ("Create Blog Category (Admin)",   ok("category",BLOG_CAT,"Blog category created"),         201),
    ("Get Blog Category (Admin)",      ok("category",BLOG_CAT,"Blog category retrieved"),       None),
    ("Update Blog Category (Admin)",   ok("category",BLOG_CAT,"Blog category updated"),         None),
    ("Delete Blog Category (Admin)",   deleted("Blog category deleted"),                         None),
    ("List Blogs (Admin)",             list_ok("blogs",[BLOG],"Blogs retrieved"),               None),
    ("Create Blog (Admin)",            ok("blog",BLOG,"Blog created"),                           201),
    ("Get Blog (Admin)",               ok("blog",BLOG,"Blog retrieved"),                         None),
    ("Update Blog (Admin)",            ok("blog",BLOG,"Blog updated"),                           None),
    ("Delete Blog (Admin)",            deleted("Blog deleted"),                                   None),
]:
    set_item(nm, resp=data, code=msg or 200)

# ══════════════════════════════════════════════════════════════════════════════
# FAQs
# ══════════════════════════════════════════════════════════════════════════════
for nm, data, code in [
    ("List FAQ Categories (Public)", list_ok("categories",[FAQ_CAT],"FAQ categories retrieved"), 200),
    ("List FAQs (Public)",           list_ok("faqs",[FAQ],"FAQs retrieved"),                     200),
    ("List FAQ Categories (Admin)",  list_ok("categories",[FAQ_CAT],"FAQ categories retrieved"), 200),
    ("Create FAQ Category (Admin)",  ok("category",FAQ_CAT,"FAQ category created"),              201),
    ("Get FAQ Category (Admin)",     ok("category",FAQ_CAT,"FAQ category retrieved"),            200),
    ("Update FAQ Category (Admin)",  ok("category",FAQ_CAT,"FAQ category updated"),              200),
    ("Delete FAQ Category (Admin)",  deleted("FAQ category deleted"),                             200),
    ("List FAQs (Admin)",            list_ok("faqs",[FAQ],"FAQs retrieved"),                     200),
    ("Create FAQ (Admin)",           ok("faq",FAQ,"FAQ created"),                                201),
    ("Get FAQ (Admin)",              ok("faq",FAQ,"FAQ retrieved"),                              200),
    ("Update FAQ (Admin)",           ok("faq",FAQ,"FAQ updated"),                                200),
    ("Delete FAQ (Admin)",           deleted("FAQ deleted"),                                      200),
]:
    set_item(nm, resp=data, code=code)

# ══════════════════════════════════════════════════════════════════════════════
# PARTNERS / CASE STUDIES
# ══════════════════════════════════════════════════════════════════════════════
for nm, data, code in [
    ("List Partners (Public)",       list_ok("partners",[PARTNER],"Partners retrieved"),         200),
    ("List Case Studies (Public)",   list_ok("case_studies",[CASE_STUDY],"Case studies retrieved"),200),
    ("Get Case Study (Public)",      ok("case_study",CASE_STUDY,"Case study retrieved"),         200),
    ("List Partners (Admin)",        list_ok("partners",[PARTNER],"Partners retrieved"),         200),
    ("Create Partner (Admin)",       ok("partner",PARTNER,"Partner created"),                    201),
    ("Get Partner (Admin)",          ok("partner",PARTNER,"Partner retrieved"),                  200),
    ("Update Partner (Admin)",       ok("partner",PARTNER,"Partner updated"),                    200),
    ("Delete Partner (Admin)",       deleted("Partner deleted"),                                  200),
    ("List Case Studies (Admin)",    list_ok("case_studies",[CASE_STUDY],"Case studies retrieved"),200),
    ("Create Case Study (Admin)",    ok("case_study",CASE_STUDY,"Case study created"),           201),
    ("Get Case Study (Admin)",       ok("case_study",CASE_STUDY,"Case study retrieved"),         200),
    ("Update Case Study (Admin)",    ok("case_study",CASE_STUDY,"Case study updated"),           200),
    ("Delete Case Study (Admin)",    deleted("Case study deleted"),                               200),
]:
    set_item(nm, resp=data, code=code)

# ══════════════════════════════════════════════════════════════════════════════
# CONTACT LOOKUPS
# ══════════════════════════════════════════════════════════════════════════════
for nm, data, code in [
    ("List Contact Industries (Admin)",  list_ok("industries",[CONTACT_IND],"Industries retrieved"), 200),
    ("Create Contact Industry (Admin)",  ok("industry",CONTACT_IND,"Industry created"),              201),
    ("Get Contact Industry (Admin)",     ok("industry",CONTACT_IND,"Industry retrieved"),            200),
    ("Update Contact Industry (Admin)",  ok("industry",CONTACT_IND,"Industry updated"),              200),
    ("Delete Contact Industry (Admin)",  deleted("Industry deleted"),                                 200),
    ("List Contact Services (Admin)",    list_ok("services",[CONTACT_SVC],"Services retrieved"),     200),
    ("Create Contact Service (Admin)",   ok("service",CONTACT_SVC,"Service created"),                201),
    ("Get Contact Service (Admin)",      ok("service",CONTACT_SVC,"Service retrieved"),              200),
    ("Update Contact Service (Admin)",   ok("service",CONTACT_SVC,"Service updated"),                200),
    ("Delete Contact Service (Admin)",   deleted("Service deleted"),                                  200),
    ("List Contact Solutions (Admin)",   list_ok("solutions",[CONTACT_SOL],"Solutions retrieved"),   200),
    ("Create Contact Solution (Admin)",  ok("solution",CONTACT_SOL,"Solution created"),              201),
    ("Get Contact Solution (Admin)",     ok("solution",CONTACT_SOL,"Solution retrieved"),            200),
    ("Update Contact Solution (Admin)",  ok("solution",CONTACT_SOL,"Solution updated"),              200),
    ("Delete Contact Solution (Admin)",  deleted("Solution deleted"),                                 200),
]:
    set_item(nm, resp=data, code=code)

# ══════════════════════════════════════════════════════════════════════════════
# VISION & MESSAGE
# ══════════════════════════════════════════════════════════════════════════════
set_item("Get Vision & Message Setting (Public)",
    resp=ok("setting", VISION_SETTING, "Vision & Message setting retrieved successfully"))
set_item("List Vision Messages (Public)",
    resp=list_ok("vision_messages", [VISION_MSG], "Vision messages retrieved successfully"))
set_item("Get Vision & Message Setting (Admin)",
    resp=ok("setting", VISION_SETTING, "Vision & Message setting retrieved successfully"))
set_item("Update Vision & Message Setting (Admin)",
    body=jbody({"title":{"en":"Our Vision & Message","ar":"رؤيتنا ورسالتنا"},"description":{"en":"Driving innovation forward","ar":"دفع الابتكار إلى الأمام"}}),
    resp=ok("setting", VISION_SETTING, "Vision & Message setting updated successfully"))
set_item("List Vision Messages (Admin)",
    resp=list_ok("vision_messages", [VISION_MSG], "Vision messages retrieved successfully"))

# Create Vision Message – clean body (no img/percentage/is_active)
set_item("Create Vision Message (Admin)",
    body=fbody([("title[en]","Our Vision"),("title[ar]","رؤيتنا"),("description[en]","Leading digital transformation"),("description[ar]","قيادة التحول الرقمي")]),
    resp=ok("vision_message", VISION_MSG, "Vision message created successfully"), code=201)

set_item("Update Vision Message (Admin)",
    body=fbody([("title[en]","Our Vision Updated"),("title[ar]","رؤيتنا محدثة"),("description[en]","Driving innovation forward"),("description[ar]","دفع الابتكار")]),
    resp=ok("vision_message", VISION_MSG, "Vision message updated successfully"))

set_item("Get Vision Message (Admin)",
    resp=ok("vision_message", VISION_MSG, "Vision message retrieved successfully"))
set_item("Delete Vision Message (Admin)",
    resp=deleted("Vision message deleted successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# METHODOLOGY
# ══════════════════════════════════════════════════════════════════════════════
set_item("Get Methodology Setting (Public)",
    resp=ok("setting", METHODOLOGY_SETTING, "Methodology setting retrieved successfully"))
set_item("List Methodologies (Public)",
    resp=list_ok("methodologies", [METHODOLOGY], "Methodologies retrieved successfully"))
set_item("Get Methodology Setting (Admin)",
    resp=ok("setting", METHODOLOGY_SETTING, "Methodology setting retrieved successfully"))
set_item("Update Methodology Setting (Admin)",
    body=jbody({"title":{"en":"Our Methodology","ar":"منهجيتنا"},"description":{"en":"How we work","ar":"كيف نعمل"}}),
    resp=ok("setting", METHODOLOGY_SETTING, "Methodology setting updated successfully"))
set_item("List Methodologies (Admin)",
    resp=list_ok("methodologies", [METHODOLOGY], "Methodologies retrieved successfully"))
set_item("Create Methodology (Admin)",
    resp=ok("methodology", METHODOLOGY, "Methodology created successfully"), code=201)
set_item("Get Methodology (Admin)",
    resp=ok("methodology", METHODOLOGY, "Methodology retrieved successfully"))
set_item("Update Methodology (Admin)",
    resp=ok("methodology", METHODOLOGY, "Methodology updated successfully"))
set_item("Delete Methodology (Admin)",
    resp=deleted("Methodology deleted successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# TEAM
# ══════════════════════════════════════════════════════════════════════════════
set_item("Get Team Setting (Public)",
    resp=ok("setting", TEAM_SETTING, "Team setting retrieved successfully"))
set_item("List Team Members (Public)",
    resp=list_ok("team_members", [TEAM_MEMBER], "Team members retrieved successfully"))
set_item("Get Team Setting (Admin)",
    resp=ok("setting", TEAM_SETTING, "Team setting retrieved successfully"))
set_item("Update Team Setting (Admin)",
    body=jbody({"title":{"en":"Our Team","ar":"فريقنا"},"description":{"en":"Meet the team","ar":"تعرف على الفريق"}}),
    resp=ok("setting", TEAM_SETTING, "Team setting updated successfully"))
set_item("List Team Members (Admin)",
    resp=list_ok("team_members", [TEAM_MEMBER], "Team members retrieved successfully"))

TEAM_FORMDATA = [
    ("title[en]","John Doe"),("title[ar]","جون دو"),
    ("position[en]","CEO"),("position[ar]","الرئيس التنفيذي"),
    ("description[en]","Experienced leader with 15+ years."),
    ("description[ar]","قائد متمرس بأكثر من 15 عامًا."),
    ("social_links[0][platform]","linkedin"),
    ("social_links[0][url]","https://linkedin.com/in/johndoe"),
    ("social_links[1][platform]","twitter"),
    ("social_links[1][url]","https://twitter.com/johndoe"),
    ("img","__file__"),("is_active","1"),
]
set_item("Create Team Member (Admin)",
    body=fbody(TEAM_FORMDATA),
    resp=ok("team_member", TEAM_MEMBER, "Team member created successfully"), code=201)
set_item("Get Team Member (Admin)",
    resp=ok("team_member", TEAM_MEMBER, "Team member retrieved successfully"))
set_item("Update Team Member (Admin)",
    body=fbody(TEAM_FORMDATA[:-2] + [("img","__file__")]),
    resp=ok("team_member", TEAM_MEMBER, "Team member updated successfully"))
set_item("Delete Team Member (Admin)",
    resp=deleted("Team member deleted successfully"))

# ── Public show team member (insert if missing) ──────────────────────────────
if not any(it["name"] == "Get Team Member (Public)" for it in items):
    insert_after("List Team Members (Public)", make_item(
        "Get Team Member (Public)", "GET",
        "{{base_url}}/api/team-members/:teamMember",
        auth=False,
        variables=[{"key":"teamMember","value":"{{teamMember}}"}],
        resp=ok("team_member", TEAM_MEMBER, "Team member retrieved successfully"),
    ))
else:
    set_item("Get Team Member (Public)",
        resp=ok("team_member", TEAM_MEMBER, "Team member retrieved successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# ABOUT US (insert if missing)
# ══════════════════════════════════════════════════════════════════════════════
if not any(it["name"] == "Get About Us Setting (Public)" for it in items):
    insert_after("Get Team Member (Public)", make_item(
        "Get About Us Setting (Public)", "GET",
        "{{base_url}}/api/about-us/setting",
        auth=False,
        resp=ok("setting", ABOUT_US, "About Us setting retrieved successfully"),
    ))
else:
    set_item("Get About Us Setting (Public)",
        resp=ok("setting", ABOUT_US, "About Us setting retrieved successfully"))

ABOUT_BODY = {
    "title":{"en":"About Industry360","ar":"عن إندستري 360"},
    "description":{"en":"We are a leading technology company.","ar":"نحن شركة تقنية رائدة."},
    "sub_title":{"en":"What We Achieved","ar":"ما حققناه"},
    "sub_description":{"en":"Our numbers speak for themselves.","ar":"أرقامنا تتحدث عن نفسها."},
    "percentage_title_1":{"en":"Customer Satisfaction","ar":"رضا العملاء"},
    "percentage_description_1":{"en":"Clients who rate us 5 stars","ar":"عملاء يمنحوننا 5 نجوم"},
    "percentage_value_1":95,
    "percentage_title_2":{"en":"Project Success","ar":"نجاح المشاريع"},
    "percentage_description_2":{"en":"On-time delivery","ar":"التسليم في الوقت المحدد"},
    "percentage_value_2":88,
    "percentage_title_3":{"en":"Growth Rate","ar":"معدل النمو"},
    "percentage_description_3":{"en":"Year-over-year growth","ar":"النمو السنوي"},
    "percentage_value_3":72,
}
if not any(it["name"] == "Get About Us Setting (Admin)" for it in items):
    items.append(make_item("Get About Us Setting (Admin)", "GET",
        "{{base_url}}/api/admin/about-us/setting",
        resp=ok("setting", ABOUT_US, "About Us setting retrieved successfully")))
    items.append(make_item("Update About Us Setting (Admin)", "POST",
        "{{base_url}}/api/admin/about-us/setting",
        body=jbody(ABOUT_BODY),
        resp=ok("setting", ABOUT_US, "About Us setting updated successfully")))
else:
    set_item("Get About Us Setting (Admin)",
        resp=ok("setting", ABOUT_US, "About Us setting retrieved successfully"))
    set_item("Update About Us Setting (Admin)",
        body=jbody(ABOUT_BODY),
        resp=ok("setting", ABOUT_US, "About Us setting updated successfully"))

# ══════════════════════════════════════════════════════════════════════════════
# PACKAGES
# ══════════════════════════════════════════════════════════════════════════════
for nm, data, code in [
    ("List Packages (Admin)",   list_ok("packages",[PACKAGE],"Packages retrieved"), 200),
    ("Create Package (Admin)",  ok("package",PACKAGE,"Package created"),             201),
    ("Get Package (Admin)",     ok("package",PACKAGE,"Package retrieved"),           200),
    ("Update Package (Admin)",  ok("package",PACKAGE,"Package updated"),             200),
    ("Delete Package (Admin)",  deleted("Package deleted"),                           200),
]:
    set_item(nm, resp=data, code=code)

# ══════════════════════════════════════════════════════════════════════════════
# Save
# ══════════════════════════════════════════════════════════════════════════════
with open(OUTPUT, "w", encoding="utf-8") as f:
    json.dump(col, f, ensure_ascii=False, indent=4)

print(f"✅ Done — {len(items)} items in collection")
