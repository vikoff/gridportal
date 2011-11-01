/bin/echo "Job started on `hostname` at `date`"
sleep 5
#./fds5_linux_32 ./box_burn_away.fds fire45
./fds5_openmp_intel_linux_32 ./forest_3.fds
/bin/tar -cvf forest_fire.tar ./*
/bin/echo "Job ended at `date`"


