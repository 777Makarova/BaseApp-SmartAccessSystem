<?php


namespace App\Entity\News;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BaseEntity;
use App\Entity\File\File;
use App\Entity\User\User;
use DateTime;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        "get" => [
            "normalization_context" => ["groups"=>["GetNews", "GetObjNews", "GetBase"]],
            "denormalization_context" => ["groups"=>["SetNews"]]
        ],
        "post" =>[
            "normalization_context" => ["groups"=>["GetNews", "GetObjNews"]],
            "denormalization_context" => ["groups"=>["SetNews"]],
//            "security"=>"is_granted('ROLE_ADMIN')"
        ],
    ],
    itemOperations: [
        "get" => [
            "normalization_context" => ["groups"=>["GetNews", "GetObjNews", "GetBase"]],
        ],
        "put" => [
            "normalization_context" => ["groups"=>["GetNews", "GetObjNews", "GetBase"]],
            "denormalization_context" => ["groups"=>["SetNews"]],
            "security"=>"is_granted('ROLE_ADMIN')"
        ],
        "delete" => [
            "security"=>"is_granted('ROLE_ADMIN')"
        ],
    ]

)]
#[ORM\Entity]
class News extends BaseEntity
{

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank]
    #[Groups(["GetNews", "GetObjNews", "SetNews"])]
    public string $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank]
    #[Groups(["GetNews", "GetObjNews", "SetNews"])]
    public string $text;

//    #[ORM\OneToMany(targetEntity: File::class)]
//    #[Groups(["GetNews", "GetObjNews", "SetNews"])]
//    public array|ArrayCollection|PersistentCollection $images;

    #[ORM\Column(type: "datetime")]
    #[Groups(["GetNews", "GetObjNews", "SetNews"])]
    #[\Symfony\Contracts\Service\Attribute\Required]
    #[Assert\NotBlank]
    public DateTime $date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $createdBy;
    private ArrayCollection $images;

    public function addImage(File $file) {
        $this->images->add($file);
    }

    public function removeImage(File $file) {
        if ($this->images->contains($file)){
            $this->images->removeElement($file);
        }
    }

    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
    }
}
