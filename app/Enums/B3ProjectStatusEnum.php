<?php

namespace App\Enums;

enum B3ProjectStatusEnum:string {
    case FOR_APPROVAL = 'for approval';
    case FOR_SECOND_APPROVAL = 'second approval';
    case FOR_FINAL_APPROVAL = 'final approval';
    case APPROVED = 'approved';
}