<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="no")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention la date d'arrivé doit etre au bon format")
     * @Assert\GreaterThan("today",message="la date d'arrivé doit ulterieure au date d'aujourd'hui",groups={"front"})
     *
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(propertyPath="startDate",message="This value should be greater than start  date",)
     * @Assert\Date(message="Attention la date de départ doit etre au bon format")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    /**
     * @throws Exception
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt=new \DateTime();
        }
        if(empty($this->amount)){
           $this->amount= $this->ad->getPrice()*$this->getDuration();
        }

    }

    public function isBookableDates(){
        $notAvailableDays = $this->ad->getNotAvailableDays();
        $bookingDays      = $this->getDays();

        foreach ( $bookingDays as $day){
            if(array_search($day,$notAvailableDays) !== false) return false;
        }

        return true;
    }

    /**
     * return array
     */
    public function getDays(){
        $days = [];
        $date = clone  $this->startDate;
        while ($date <= $this->endDate){
            $days[]=$date->format('d/m/Y');
            $date->add(new \DateInterval("P1D"));
        }
        return $days;
    }


    public function getDuration(){
        $diff=$this->endDate->diff($this->startDate);
        return $diff->days;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
