# Module 5 Assignment 5A AI Method Critique

Student: Serge Hall

Topic: SERGIOARTG photography booking and pricing workflow

## Method Generated With AI

The AI-assisted method is `getPriorityLabel()`.

## Exact Prompt

Write a beginner-friendly PHP class method named getPriorityLabel for a photography booking object. The object has properties named status, rushDelivery, shootDate, and a method named calculateDepositDueCents(). Return Done when the booking status is completed, High priority when the shoot is within two days or rush delivery is true, Waiting for deposit when a deposit is still due, and Normal priority otherwise.

## Raw AI-Generated Code

```php
public function getPriorityLabel() {
    if ($this->status == "completed") {
        return "Done";
    }

    $today = new DateTime();
    $shoot = new DateTime($this->shootDate);
    $days = $today->diff($shoot)->days;

    if ($days <= 2 || $this->rushDelivery == true) {
        return "High priority";
    }

    if ($this->calculateDepositDueCents() > 0) {
        return "Waiting for deposit";
    }

    return "Normal priority";
}
```

## Critique

Security: The raw method does not run SQL or print unescaped client input, so the direct security risk is low. The final page still escapes all rendered output with `htmlspecialchars()` because client names, notes, and booking details could become user-controlled in a real app.

Efficiency: The method is efficient. It only checks status, date distance, rush delivery, and deposit due. There is no loop or database call.

Correctness: The raw method uses `$today->diff($shoot)->days`, which returns an absolute day count. That means an overdue shoot could be treated like a future shoot. My final version uses a signed helper method, `daysUntilShoot()`, so past and future dates are handled correctly.

Style: The raw code uses loose comparison (`==`), no return type, mutable `DateTime`, and double quotes. I changed it to strict comparison (`===`), added `: string`, reused class helper methods, and matched the style of the rest of the PHP assignment.

Changes made: I added a typed return value, replaced loose comparisons, reused the signed date helper, connected the method to the real deposit calculation, and displayed the result through the interactive UI button labeled `Run getPriorityLabel()`.
