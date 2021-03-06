<?php

namespace App\Modules\Company\Http\Controllers;

use App\Http\Controllers\BaiscController;
use App\Model\Area;
use App\Model\Company;
use App\Model\MainCategory;
use Illuminate\Http\Request;
use Cache;

class CompanyController extends BaiscController
{

    /**
     * 企业
     * @param Request $request
     * @return mixed
     */
    public function company(Request $request)
    {
        $company = Company::whereId($this->company_id)->first();
        $company->category_name = MainCategory::whereId($company->category_id)->value('name');

        $data = array(
            'company' => $company
        );
        return $this->success($data);
    }

    /**
     * 企业修改
     * @param Request $request
     * @return mixed
     */
    public function companyUpdate(Request $request)
    {
        $company = Company::whereId($this->company_id)->first();
        if($request->post('name','')){
            $company->update(['name' => $request->post('name')]);
        }
        if($request->post('logo','')){
            $company->update(['logo' => $request->post('logo')]);
        }
        if($request->post('telphone','')){
            $company->update(['telphone' => $request->post('telphone')]);
        }
        if($request->post('company_address','')){
            $company->update(['area_info' => $request->post('area_info'),'company_address' => $request->post('company_address')]);
        }

        return $this->message('修改成功');
    }

}
