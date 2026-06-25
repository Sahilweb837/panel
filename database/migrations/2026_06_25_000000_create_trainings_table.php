&lt;?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table-&gt;bigIncrements('id');
            $table-&gt;string('slip_no', 60)-&gt;unique();
            $table-&gt;string('name', 100);
            $table-&gt;string('father_name', 100)-&gt;nullable();
            $table-&gt;string('email', 100);
            $table-&gt;string('college', 150)-&gt;nullable();
            $table-&gt;string('mobile', 20);
            $table-&gt;unsignedBigInteger('course_id');
            $table-&gt;string('duration', 50);
            $table-&gt;decimal('fees', 10, 2)-&gt;default(0.00);
            $table-&gt;string('payment_method', 50)-&gt;default('Cash');
            $table-&gt;date('payment_date');
            $table-&gt;unsignedBigInteger('created_by');
            $table-&gt;softDeletes();
            $table-&gt;timestamps();

            $table-&gt;foreign('course_id', 'trainings_course_id_foreign')
                -&gt;references('id')-&gt;on('courses')
                -&gt;onDelete('cascade');

            $table-&gt;foreign('created_by', 'trainings_created_by_foreign')
                -&gt;references('id')-&gt;on('users')
                -&gt;onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
