# Module 6 Assignment 6A: MVC-Based PHP Application

Course: CS 85 - PHP Programming

Student: Siarhei Hancharou

## App Description

This project is a small MVC-based PHP application for planning a SERGIOARTG photography booking. The app lets a user enter a client name, service type, package, session hours, edited photo count, location type, deposit paid, rush delivery option, and project note. The application calculates a quote total, deposit due, booking complexity, and workflow decision.

The topic is personal because it connects directly to my real photography platform, SERGIOARTG, where photography clients can explore work and request sessions.

Live site: https://sergioartg.com

## Setup Instructions

From the module folder:

```bash
cd assignments/module6a
composer dump-autoload
php -S 127.0.0.1:8086 -t public
```

Inside the Laravel coursework project, the assignment is also available through:

```text
http://127.0.0.1:8000/assignments/module6a/public/index.php
```

## MVC Structure

```text
public/index.php
src/Controllers/BookingPlannerController.php
src/Models/PhotographyProject.php
views/booking-planner.php
composer.json
README.md
```

## Composer PSR-4 Autoloading

The module uses PSR-4 autoloading:

```json
{
  "autoload": {
    "psr-4": {
      "Cs85\\Module6A\\": "src/"
    }
  }
}
```

The root Laravel project also maps the same namespace so the assignment can run through the shared coursework route.

## Reflection

I chose a photography booking planner because it connects directly to my real life and portfolio work. I am building SERGIOARTG as a photography platform with a public portfolio, booking requests, pricing, availability, orders, payments, and client communication. For this assignment, I reduced that larger product idea into a small PHP MVC application that is easy to understand and test. The app lets a user change booking inputs and immediately see the quote total, deposit due, project complexity, and next workflow decision.

The Model is `PhotographyProject`. It owns the business rules: package pricing, extra hours, extra edited photos, location fees, rush delivery, service multipliers, deposit calculation, and workflow decisions. The Controller is `BookingPlannerController`. It reads request input, validates values, creates the Model, and passes clean data into the View. The View is `booking-planner.php`. It displays the form, validation messages, quote summary, and MVC structure.

The hardest part was deciding where each responsibility belongs. In a one-file PHP script, it is tempting to put validation, calculations, and HTML together. MVC forced me to separate those concerns. The Model should not know about form fields or HTML. The View should not calculate prices. The Controller should connect the request to the Model and View without becoming too large.

I learned that MVC makes code easier to reason about because each file has a clear job. Composer autoloading also made the project cleaner because classes can be loaded by namespace instead of repeated `require_once` statements. This is very close to how professional frameworks like Laravel organize code, but the smaller project makes the pattern easier to see.

## AI Code Review and Critique

### Prompt

Write a beginner-friendly PHP method for a photography booking model named workflowDecision. It should return a string. If deposit is still due, tell the user to send a quote and collect the deposit. If rush delivery is true or the location is out of city, tell the user to review logistics. Otherwise, say the booking is ready to confirm.

### Raw AI Output

```php
public function workflowDecision() {
    if ($this->depositDueCents() > 0) {
        return "Send quote and collect deposit before confirming.";
    }

    if ($this->rushDelivery == true || $this->locationType == "out_of_city") {
        return "Review logistics before confirming.";
    }

    return "Ready to confirm booking.";
}
```

### Critique

The AI-generated method was useful because the decision logic matched the basic business rule I wanted. It was efficient because it used direct condition checks and did not use unnecessary loops or database queries. The security risk was low because the method does not process raw HTML or SQL. However, the raw output used loose comparison with `==`, did not include a return type, and the messages were too generic for my app.

I changed the method to use `: string`, strict comparison style, clearer SERGIOARTG booking language, and more specific output. I also kept the deposit calculation inside the Model so the View does not need to know how deposits are computed.
