<?php

namespace App\Services;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isNull;

class UserService
{

    public static function affiliations()
    {
        $afiliations =  [
            'Riken', "Institut Teknologi Sepuluh Nopember",
            "Universitas Padjadjaran", "Institut Teknologi Bandung",
            "Universitas Gadjah Mada", "Universitas Indonesia", "Another"
        ];
        return array_combine($afiliations,$afiliations);
    }

    public static function isNotVerifyEmail(Request $request)
    {
        $verifyEmail = Auth::user()->email_verified_at;
        if(is_null($verifyEmail)){
            throw ValidationException::withMessages([
                'email' => 'Email has registered, but not verified yet. <a id="resend-verification" href="' . route('verification.send') . '">Resend Verification</a>',
            ]);
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect(route('login'));
        }
    }

    private function getUsers($tipeUser = 1, $search = '')
    {
        $isAdmin = $tipeUser == 1;
        $select = ['users.name','users.email', 'email_verified_at','users.id','a.*'];
        if($isAdmin){
            $select = ['users.name','email','email_verified_at' ,'id', 'a.*'];
        }
        return User::join('users_details AS a', 'id', 'a.user_id')->where('users.is_admin',$tipeUser)
                ->when($isAdmin, function($query) use ($search){
                    if ($search == 'verified' || $search == 'unverified') {
                        if ($search == 'verified') {
                            $query->whereNotNull('email_verified_at');
                        } else {
                            $query->whereNull('email_verified_at');
                        }
                    }
                })
                ->orderBy('users.name')->get($select);
    }

    public function listUser($tipeUser){
        $isAdmin = $tipeUser == 1;
        $data = $this->getUsers($tipeUser);
        return DataTables::of($data)->addColumn('action', function ($row) use($isAdmin){
            $btnEditOrDel = $isAdmin ? '<a class="btn btn-primary btn-xs btn-edit" data-id="'.$row->id.'" title="Edit Data">
                    <i class="fa fa-pencil"></i></a>' : '<a class="btn btn-success btn-xs btn-download" data-id="'.$row->id.'" title="Download Data">
                    <i class="fa fa-download"></i></a>';
            $titleDel = $isAdmin ? 'Delete User' : 'Delete Participants';
            return $btnEditOrDel.'&nbsp;<button data-id="' . $row->id . '" class="btn btn-danger btn-xs btn-hapus"
                title="'.$titleDel.'"><i class="fa fa-trash-o"></i></button>';
        })->addColumn('verified_at',function($row){
            return empty($row->email_verified_at) ? 'Unverified' : 'Verified';
        })->rawColumns(['action','verified_at'])->make(true);
    }

    public function simpanAdmin(StoreUserRequest $request)
    {
        $data = $request->validated();
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $data['is_admin'] = 1;
        $user = User::updateOrCreate(['id' => $request->input('id')], $data);
        return UserDetail::updateOrCreate(['user_id' => $user->id],[
            'user_id'       => $user->id,
            'affiliation'   => $request->input('affiliation'),
            'phonenumber'   => $request->input('phonenumber'),
        ]);
    }

    public function changePass(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::find($data['id']);
        $user->password = Hash::make($data['password']);
        return $user->save();
    }

    public function reportExcel()
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $defaultStyle = (new StyleBuilder)->setFontName('Arial')->setFontSize(11)->build();
        $writer->setDefaultRowStyle($defaultStyle);

        $styleHeader = new Style();
        $styleHeader->setFontBold();
        $styleHeader->setFontName('Arial Narrow');
        $styleHeader->setShouldWrapText(false);
        $styleHeader->setFontSize(12);
        $writer->openToBrowser('list-participants.xlsx');
        $writer->setColumnWidth(10, 1); // title
        $writer->setColumnWidth(40, 2); // Name
        $writer->setColumnWidth(50, 3); // Address
        $writer->setColumnWidth(20, 4); // Presentation Role
        $writer->setColumnWidth(20, 5); // country
        $writer->setColumnWidth(25, 6); // Email
        $writer->setColumnWidth(25, 7); // Status Email
        $writer->setColumnWidth(25, 8); // Affiliation
        $writer->setColumnWidth(20, 9); // Mobile Number
        $writer->setColumnWidth(20, 10); // Phone Number

        $title1 = WriterEntityFactory::createCell('List Participants', $styleHeader);
        $singleRow = WriterEntityFactory::createRow([$title1]);
        $writer->addRow($singleRow);

        $writer->addRow(WriterEntityFactory::createRow());
        $border = (new BorderBuilder)->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)->build();
        $styleHeader->setBorder($border);
        $styleHeader->setCellAlignment(CellAlignment::CENTER);
        $styleHeader->setBorder($border);
        $styleHeader->setBackgroundColor(Color::rgb(218, 227, 243));

        $namaKolom = ['Title', 'Name', 'Address', 'Presentation Role','Country', 'Email', 'Status Email','Affiliation',
                        'Mobile Number', 'Phone Number'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);
        $participantsData = $this->getUsers(0);
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($participantsData as $row) {
            $verifiedText = empty($row->email_verified_at)? 'Unverified' : 'Verified';
            $perBaris = [
                WriterEntityFactory::createCell($row->title, $styleCenter),
                WriterEntityFactory::createCell($row->name, $styleLeft),
                WriterEntityFactory::createCell($row->address, $styleLeft),
                WriterEntityFactory::createCell($row->presentation, $styleCenter),
                WriterEntityFactory::createCell($row->country, $styleLeft),
                WriterEntityFactory::createCell($row->email, $styleLeft),
                WriterEntityFactory::createCell($verifiedText, $styleCenter),
                WriterEntityFactory::createCell($row->affiliation, $styleCenter),
                WriterEntityFactory::createCell($row->mobilenumber, $styleCenter),
                WriterEntityFactory::createCell($row->phonenumber, $styleCenter),
            ];
            $writer->addRow(WriterEntityFactory::createRow($perBaris));
        }
        $writer->close();
    }
}
