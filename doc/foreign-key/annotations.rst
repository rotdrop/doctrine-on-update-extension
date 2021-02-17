Annotations
===========

.. code-block:: php

    <?php

    namespace Acme\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use CJH\Doctrine\Mapping\Annotation as CJH;

    /**
     * @ORM\Entity
     */
    class Foo
    {
       /**
        * @ORM\ManyToOne(targetEntity="Bar", inversedBy="foos")
        * @ORM\JoinColumn(referencedColumnName="bar_col")
        * @ORM\Id
        * @CJH\ForeignKey(targetEntity="Bar", referencedColumnName="bar_col", onUpdate="cascade")
        *
        * Optional: name="local_column_name", constraintName="unique_name_for_the_constraint"
        */
       protected $bar;
