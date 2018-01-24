sym3
====

A Symfony project created on June 17, 2016, 7:43 am.

Workflow
===
1- Aprrover
---
- If the max amount field is empty, the claim has to go through next line of approver.
- Max Amount means if the amount of the claim is larger than this amount, the approver will not be selected and the Code moves on to the next line of Approver.

Configurations
===
- PHP Configuration
    - [2018-01-23 09:22:35] request.CRITICAL: Uncaught PHP Exception Symfony\Component\Debug\Exception\OutOfMemoryException: "Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 12014348 bytes)" at /var/www/performance/claim-live/vendor/imagine/imagine/lib/Imagine/Image/Metadata/ExifMetadataReader.php line 84 {"exception":"[object] (Symfony\\Component\\Debug\\Exception\\OutOfMemoryException(code: 0): Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 12014348 bytes) at /var/www/performance/claim-live/vendor/imagine/imagine/lib/Imagine/Image/Metadata/ExifMetadataReader.php:84)"} []

