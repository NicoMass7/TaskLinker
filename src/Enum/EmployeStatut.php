<?php

namespace App\Enum;

enum EmployeStatut: string
{
  case Cdi = 'CDI';
  case Cdd = 'CDD';
  case Freelance = 'Freelance';

  public function getLabel(): string
  {
    return match ($this) {
      self::Cdi => 'CDI',
      self::Cdd => 'CDD',
      self::Freelance => 'Freelance',
    };
  }
}
