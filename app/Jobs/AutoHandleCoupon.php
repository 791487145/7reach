<?php

namespace App\Jobs;

use App\Model\CouponTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AutoHandleCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $coupon;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //获得状态为有效且时间已过期的优惠券
        $this->coupon=CouponTemplate::where(['coupon_t_state'=>1,['coupon_t_end_date','<',time()]])->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if(empty($this->coupon)){
            return;
        }
        $this->coupon->each(function ($item,$key){
            $item->update(['coupon_t_state'=>2]);
        });
    }
}
