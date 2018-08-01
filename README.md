# river
Stupidest river crossing puzzle solver. Just run from console.

river.php - russian variant
Response(in reverse order):
```sh
                                                    B ded kapusta koza volk
B ded koza                                            kapusta volk
  koza                                              B ded kapusta volk
B ded koza volk                                       kapusta
  volk                                              B ded kapusta koza
B ded kapusta volk                                    koza
  kapusta volk                                      B ded koza
B ded kapusta koza volk
```

river2.php - japanese variant
Response(in reverse order):
```sh
                                                              B daughter1 daughter2 father mother murder police son1 son2
B murder police                                                 daughter1 daughter2 father mother son1 son2
  murder                                                      B daughter1 daughter2 father mother police son1 son2
B murder police son2                                            daughter1 daughter2 father mother son1
  son2                                                        B daughter1 daughter2 father mother murder police son1
B father son1 son2                                              daughter1 daughter2 mother murder police
  son1 son2                                                   B daughter1 daughter2 father mother murder police
B father mother son1 son2                                       daughter1 daughter2 murder police
  father son1 son2                                            B daughter1 daughter2 mother murder police
B father murder police son1 son2                                daughter1 daughter2 mother
  murder police son1 son2                                     B daughter1 daughter2 father mother
B father mother murder police son1 son2                         daughter1 daughter2
  father murder police son1 son2                              B daughter1 daughter2 mother
B daughter2 father mother murder police son1 son2               daughter1
  daughter2 father mother son1 son2                           B daughter1 murder police
B daughter1 daughter2 father mother police son1 son2            murder
  daughter1 daughter2 father mother son1 son2                 B murder police
B daughter1 daughter2 father mother murder police son1 son2
```
