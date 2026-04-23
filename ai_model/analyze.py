#!/usr/bin/env python3
import sys, json

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"user_type":"Undecided","confidence":0.0}))
        return

    input_json = sys.argv[1]
    try:
        payload = json.loads(input_json)
    except Exception:
        print(json.dumps({"user_type":"Undecided","confidence":0.0}))
        return

    # naive heuristic: look into responses for keywords
    text = ""
    for r in payload.get('responses', []):
        text += " " + (r.get('answer_text') or "") + " " + str(r.get('option_id') or "")

    text = text.lower()
    user_type = "Undecided"
    confidence = 0.0

    if "high" in text or "high-school" in text or "high school" in text:
        user_type = "high_school"
        confidence = 0.85
    elif "college" in text or "university" in text:
        user_type = "graduating_student"
        confidence = 0.85
    elif "fresh" in text or "graduate" in text:
        user_type = "fresh_graduate"
        confidence = 0.8
    elif "switch" in text or "working" in text:
        user_type = "career_switcher"
        confidence = 0.8
    else:
        user_type = "undecided"
        confidence = 0.5

    print(json.dumps({"user_type": user_type, "confidence": confidence, "payload": {"note": "placeholder model"}}))

if __name__ == "__main__":
    main()
