Annotations
===========

Please refer to https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/annotations.md#em-setup

.. code-block:: php
    use CJH\Doctrine\Extensions as CJH;
    // ...
    CJH\Setup::registerAnnotations();
    // ...
    $foreignKeyListener = new CJH\ForeignKey\Listener($entityManager);
    $foreignKeyListener->setAnnotationReader($cachedAnnotationReader);
    $evm->addEventSubscriber($foreignKeyListener);
